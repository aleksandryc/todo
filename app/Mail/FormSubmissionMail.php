<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FormSubmissionMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    public $pdfPath;
    public $userAttachments;
    /**
     * Create a new message instance.
     */
    public function __construct($pdfData, $pdfPath, $userAttachments = [])
    {
        $this->data = $pdfData;
        $this->pdfPath = $pdfPath;
        $this->userAttachments = $userAttachments;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Form Submission Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.form-submission-mail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        if (file_exists(storage_path('app/' . $this->pdfPath))) {
            $attachments[] = Attachment::fromPath(storage_path('app/' . $this->pdfPath))
                ->as('form_submission.pdf')
                ->withMime('application/pdf');
        }
        foreach ($this->userAttachments as $key => $attachmentPath) {

            if (file_exists(storage_path($attachmentPath))) {
                $fileName = basename((storage_path($attachmentPath)));
                $mimeType = \Illuminate\Support\Facades\File::mimeType((storage_path($attachmentPath)));

                $attachments[] = Attachment::fromPath(storage_path($attachmentPath))
                ->as($fileName)
                ->withMime($mimeType);

            }
        }

        return $attachments;
    }

    public function build()
    {
        return $this->view('mail.form-submission-mail')->subject('New Form Submitted');
    }
}
