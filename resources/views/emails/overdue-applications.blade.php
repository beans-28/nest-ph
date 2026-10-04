<!doctype html>
<html>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f4f6f4; padding:40px 0; margin:0;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;">
        <tr>
            @include('emails.partials.header')
        </tr>
        <tr>
            <td style="padding:32px;">
                <h2 style="color:#292420;margin:0 0 12px;font-size:20px;">Applications waiting for review</h2>
                <p style="color:#4b5f4c;font-size:14px;line-height:1.6;margin:0 0 20px;">
                    The following {{ $applications->count() === 1 ? 'application has' : 'applications have' }} been pending for
                    {{ \App\Console\Commands\NotifyOverdueApplications::DAYS }} days or more. Please approve or reject
                    {{ $applications->count() === 1 ? 'it' : 'them' }} so the applicant isn't left waiting.
                </p>

                <div style="background:#eeeded;border-radius:10px;padding:8px 18px;margin:0 0 20px;">
                    @foreach($applications->take(10) as $application)
                        <p style="margin:10px 0;font-size:14px;color:#292420;">
                            <strong style="color:#567357;">#{{ $application->id }}</strong>
                            &middot; {{ $application->full_name }}
                            <span style="color:#7a7a7a;font-size:12px;">&middot; submitted {{ $application->created_at->format('M j, Y') }} ({{ (int) $application->created_at->diffInDays(now()) }} days ago)</span>
                        </p>
                    @endforeach
                    @if($applications->count() > 10)
                        <p style="margin:10px 0;font-size:13px;color:#7a7a7a;">and {{ $applications->count() - 10 }} more.</p>
                    @endif
                </div>

                <p style="margin:0 0 20px;">
                    <a href="{{ url('/applications') }}" style="display:inline-block;background:#567357;color:#ffffff;text-decoration:none;padding:12px 20px;border-radius:8px;font-size:14px;font-weight:600;">Review applications</a>
                </p>
            </td>
        </tr>
        @include('emails.partials.footer')
    </table>
</body>
</html>
