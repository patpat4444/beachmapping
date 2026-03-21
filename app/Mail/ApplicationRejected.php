<?php

namespace App\Mail;

use App\Models\BeachOwnerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationRejected extends Mailable
{
    use Queueable, SerializesModels;

    public BeachOwnerApplication $application;

    public function __construct(BeachOwnerApplication $application)
    {
        $this->application = $application;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Dagat Ta bAI - Application Status Update',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.application-rejected',
            with: [
                'application' => $this->application,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
