<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Application $application,
        public string $email,
        public ?string $temporaryPassword // null when reusing an existing account
    ) {
    }

    public function build(): self
    {
        return $this->subject('Your NEST.PH application has been approved!')
            ->view('emails.application-approved');
    }
}
