<?php

namespace App\Mail;

use App\Models\DormitoryProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $code)
    {
    }

    public function build(): self
    {
        return $this->subject('Your ' . DormitoryProfile::current()->dorm_name . ' Password Reset Code')
            ->view('emails.password-reset-code');
    }
}
