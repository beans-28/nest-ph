<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DormitoryHouseRule extends Model
{
    protected $fillable = [
        'rule_text',
        'sort_order',
    ];
}
