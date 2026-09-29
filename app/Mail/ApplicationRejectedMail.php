<?php

namespace App\Mail;

use App\Models\DormitoryProfile;
use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Application $application)
    {
    }

    public function build(): self
    {
        return $this->subject('Update on your ' . DormitoryProfile::current()->dorm_name . ' application')
            ->view('emails.application-rejected');
    }
}
