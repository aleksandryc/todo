<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FormSubmissionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $pdfPath;
    public $embeddedImages;
    public $attachmentList;


    public function __construct($pdfPath, $attachments, $formData, $embeddedImages)
    {
        $this->data = $formData;
        $this->pdfPath = $pdfPath;
        $this->embeddedImages = $embeddedImages;
        $this->attachmentList = $attachments;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.form-submission-mail')->subject('New Form Submitted')->attach(storage_path($this->pdfPath));
    }
}
