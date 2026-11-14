<?php

namespace App\Mail;

use App\Models\DormitoryProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * One email template for every billing notice (new bill, due soon, grace
 * period, overdue stages). The wording comes from BillingEmailService.
 *
 * For a guardian ($forGuardian), the email states only the amount, due
 * date and penalty -- no tenant name or other details (Tenant Agreement 9.3).
 */
class BillingNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @param array<string,string> $details label => value rows shown in a box */
    public function __construct(
        public string $subjectLine,
        public string $heading,
        public string $intro,
        public array $details,
        public string $closing,
        public bool $forGuardian = false,
    ) {
    }

    public function build(): self
    {
        return $this->subject($this->subjectLine . ' - ' . (DormitoryProfile::current()->dorm_name ?? 'NEST PH'))
            ->view('emails.billing-notice');
    }
}
