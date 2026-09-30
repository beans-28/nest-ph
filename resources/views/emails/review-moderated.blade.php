<!doctype html>
<html>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f4f6f4; padding:40px 0; margin:0;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;">
        <tr>
            @include('emails.partials.header')
        </tr>
        <tr>
            <td style="padding:32px;">
                <h2 style="color:#292420;margin:0 0 12px;font-size:20px;">Hi {{ $review->tenant?->first_name ?? 'there' }},</h2>

                @if($review->status === 'published')
                    <p style="margin:0 0 16px;font-size:14px;color:#292420;line-height:1.6;">Thank you for sharing your experience. Your review has been checked and is now shown on our public page.</p>
                @elseif($review->status === 'hidden')
                    <p style="margin:0 0 16px;font-size:14px;color:#292420;line-height:1.6;">Your review is not shown on our public page for now. Our staff may review it again later.</p>
                @else
                    <p style="margin:0 0 16px;font-size:14px;color:#292420;line-height:1.6;">Your review has been removed from our public page because it did not meet our review guidelines.</p>
                @endif

                @if($review->status !== 'published' && $review->moderation_note)
                    <div style="background:#eeeded;border-radius:10px;padding:16px 18px;margin:0 0 20px;">
                        <p style="margin:0 0 4px;font-size:12px;color:#7a7a7a;text-transform:uppercase;letter-spacing:0.04em;">Note from our staff</p>
                        <p style="margin:0;font-size:14px;color:#292420;line-height:1.6;white-space:pre-line;">{{ $review->moderation_note }}</p>
                    </div>
                @endif

                <p style="color:#8a9690;font-size:12px;margin:0;">
                    Questions about this? Just reply to this email.
                </p>
            </td>
        </tr>
        @include('emails.partials.footer')
    </table>
</body>
</html>
