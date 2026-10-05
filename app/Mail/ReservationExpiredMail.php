<?php

namespace App\Mail;

use App\Models\BillingStatement;
use App\Models\DormitoryProfile;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationExpiredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Tenant $tenant, public BillingStatement $bill)
    {
    }

    public function build(): self
    {
        return $this->subject('Your reservation has expired - ' . DormitoryProfile::current()->dorm_name)
            ->view('emails.reservation-expired');
    }
}
