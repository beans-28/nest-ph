<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** One billing email sent (or attempted) to a tenant or their guardian. */
class BillingEmailLog extends Model
{
    protected $fillable = ['tenant_id', 'billing_id', 'kind', 'recipient_type', 'email', 'status', 'error'];
}
