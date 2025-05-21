<?php

namespace App\Mail\UserForms;

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
    public $mailRecipients;
    public $ccRecipients;

    /**
     * Create a new message instance.
     */
    public function __construct($pdfData, $pdfPath, $userAttachments = [], $mailRecipients = [], $ccRecipients = [])
    {
        $this->data = $pdfData;
        $this->pdfPath = $pdfPath;
        $this->userAttachments = $userAttachments;
        $this->mailRecipients = $mailRecipients;
        $this->ccRecipients = $ccRecipients;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: 'aleksandrb@eliaswoodwork.com',
            cc: '',
            subject: 'Form Submission Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.user-forms.form-submission-mail',
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
        foreach ($this->userAttachments as $attachmentPath) {
            $fullPath = storage_path('app/' . $attachmentPath);
            if (file_exists($fullPath)) {
                $fileName = basename($fullPath);
                $mimeType = \Illuminate\Support\Facades\File::mimeType($fullPath);

                $attachments[] = Attachment::fromPath($fullPath)
                ->as($fileName)
                ->withMime($mimeType);

            }
        }
        return $attachments;
    }
}
