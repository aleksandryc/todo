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
    public $attachments;
    /**
     * Create a new message instance.
     */
    public function __construct($pdfData, $pdfPath)
    {
        $this->data = $pdfData;
        $this->pdfPath = $pdfPath;
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
        $attachments =[];

        if (file_exists(storage_path($this->pdfPath))) {
            $attachments[] = Attachment::fromPath(storage_path($this->pdfPath))
            ->as('form_submission.pdf')
            ->withMime('application/pdf');
        }
        if ($this->data['fields']['files']) {
            foreach ($this->data['fields']['files'] as $key => $file) {
                if (file_exists($file) ) {
                    $fileName = basename($file);
                    $mimeType = \Illuminate\Support\Facades\File::mimeType($file);
                    $attachments[] = Attachment::fromPath("app/public/" . $file)
                    ->as($fileName)
                    ->withMime($mimeType);
                }
            }
        }
        return $attachments;
    }

    public function build()
    {
        return $this->view('mail.form-submission-mail')->subject('New Form Submitted');
    }
}
