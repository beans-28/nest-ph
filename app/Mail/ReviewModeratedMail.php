<?php

namespace App\Mail;

use App\Models\DormitoryProfile;
use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Tells a tenant what an admin decided about their review (published,
 * hidden or removed). Sent by ReviewModerationController::decide() only
 * when the status actually changes.
 */
class ReviewModeratedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Review $review)
    {
    }

    public function build(): self
    {
        $subjects = [
            'published' => 'Your review is now public',
            'hidden' => 'Your review is not public for now',
            'removed' => 'Your review was removed',
        ];

        return $this->subject(($subjects[$this->review->status] ?? 'An update on your review') . ' - ' . DormitoryProfile::current()->dorm_name)
            ->view('emails.review-moderated');
    }
}
