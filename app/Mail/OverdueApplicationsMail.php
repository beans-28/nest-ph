<?php

namespace App\Mail;

use App\Models\DormitoryProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class OverdueApplicationsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Collection $applications)
    {
    }

    public function build(): self
    {
        $count = $this->applications->count();

        return $this->subject($count . ' application' . ($count === 1 ? ' has' : 's have') . ' been waiting 3+ days - ' . DormitoryProfile::current()->dorm_name)
            ->view('emails.overdue-applications');
    }
}
