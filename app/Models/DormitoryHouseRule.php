<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DormitoryHouseRule extends Model
{
    /**
     * section groups rules under a heading (e.g. "A. Conduct and Respect")
     * on the signed Rules and Regulations. Optional: rules without one are
     * listed under "General".
     */
    protected $fillable = [
        'section',
        'rule_text',
        'sort_order',
    ];
}
