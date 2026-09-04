<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Table 15 (Add New Tenant), step 8.5 — sent when an admin registers a
 * walk-in tenant directly, as opposed to ApplicationApprovedMail which
 * covers tenants who came through the online Apply for Occupancy flow.
 */
class TenantAccountCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Tenant $tenant,
        public string $temporaryPassword
    ) {
    }

    public function build(): self
    {
        return $this->subject('Your NEST.PH tenant account has been created')
            ->view('emails.tenant-account-created');
    }
}