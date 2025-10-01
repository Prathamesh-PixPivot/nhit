<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $ccEmails;
    public $note;

    public function __construct($data, $ccEmails, $note)
    {
        $this->data = $data;
        $this->ccEmails = $ccEmails;
        $this->note = $note;
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Payment Advice from ' . ($this->data['entity_name'] ?? 'Unknown'));
    }
    public function build()
    {
        return $this->cc($this->ccEmails)
            ->view('email.utrMail')
            ->with([
                'data' => $this->data,
                'note' => $this->note,
            ]);
    }
    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(view: 'email.utrMail');
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
