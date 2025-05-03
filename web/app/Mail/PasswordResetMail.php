<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetUrl;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($resetUrl, $user)
    {
        $this->resetUrl = $resetUrl;
        $this->user = $user;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Reset Your Password')
                    ->view('emails.password-reset');
    }
}