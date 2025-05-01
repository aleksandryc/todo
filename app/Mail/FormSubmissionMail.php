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
    protected $pdfPath;
    public $embeddedImages;
    protected $attachmentList;


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
        $mail = $this->view('mail.form-submission-mail')
            ->subject('New Form Submitted')
            ->attach(storage_path($this->pdfPath));

        if(is_array($this->attachmentList)) {
            foreach ($this->attachmentList as $attachmentPath) {
                $mail->attach($attachmentPath);
            }
        }elseif (is_string($this->attachmentList)) {
            $mail->attach($$this->attachmentList);
        }
        return $mail;
    }
}
