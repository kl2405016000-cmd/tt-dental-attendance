<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public bool $isAdmin;

    public function __construct(string $otp, bool $isAdmin = false)
    {
        $this->otp = $otp;
        $this->isAdmin = $isAdmin;
    }

    public function envelope(): Envelope
    {
        $from = null;
        if ($this->isAdmin && config('mail.admin_from.address')) {
            $from = new Address(
                config('mail.admin_from.address'),
                config('mail.admin_from.name', 'TT Dental Lab (Admin Portal)')
            );
        }

        return new Envelope(
            from: $from,
            subject: ($this->isAdmin ? '[Admin Security] ' : '') . 'Your Password Reset OTP - TT Dental Lab',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            with: [
                'otp' => $this->otp,
                'isAdmin' => $this->isAdmin,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
