<?php

namespace App\Mail;

use App\Models\BillingStatement;
use App\Models\DormitoryProfile;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Stage 4 fallback: sent to the TENANT when their emergency contact did
 * not agree to billing reminders (Tenant Agreement 9.3).
 */
class DelinquencyFinalNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Tenant $tenant, public BillingStatement $bill)
    {
    }

    public function build(): self
    {
        return $this->subject('Overdue balance notice - ' . DormitoryProfile::current()->dorm_name)
            ->view('emails.delinquency-final-notice');
    }
}
