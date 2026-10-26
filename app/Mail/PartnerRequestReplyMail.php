<?php

namespace App\Mail;

use App\Models\PartnerRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PartnerRequestReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PartnerRequest $partnerRequest)
    {
    }

    public function build(): self
    {
        return $this->subject('NEST.PH for ' . $this->partnerRequest->dorm_name)
            ->view('emails.partner-request-reply');
    }
}
