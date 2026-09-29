<?php

namespace App\Mail;

use App\Models\DormitoryProfile;
use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationReapplicationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Application $application)
    {
    }

    public function build(): self
    {
        return $this->subject('Please resubmit your ' . DormitoryProfile::current()->dorm_name . ' application')
            ->view('emails.application-reapplication');
    }
}
