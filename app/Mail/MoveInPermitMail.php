<?php

namespace App\Mail;

use App\Models\DormitoryProfile;
use App\Models\LeaseContract;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MoveInPermitMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Tenant $tenant, public ?LeaseContract $contract)
    {
    }

    public function build(): self
    {
        return $this->subject('Your Move-In Permit - ' . DormitoryProfile::current()->dorm_name)
            ->view('emails.move-in-permit');
    }
}
