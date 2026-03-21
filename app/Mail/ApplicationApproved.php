<?php

namespace App\Mail;

use App\Models\BeachOwnerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public BeachOwnerApplication $application;
    public string $pin;

    public function __construct(BeachOwnerApplication $application, string $pin)
    {
        $this->application = $application;
        $this->pin = $pin;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Dagat Ta bAI - Your Application is Approved!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.application-approved',
            with: [
                'application' => $this->application,
                'pin' => $this->pin,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
