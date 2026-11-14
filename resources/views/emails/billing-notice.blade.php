<!doctype html>
<html>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f4f6f4; padding:40px 0; margin:0;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;">
        <tr>
            @include('emails.partials.header')
        </tr>
        <tr>
            <td style="padding:32px;">
                <h2 style="color:#292420;margin:0 0 12px;font-size:20px;">{{ $heading }}</h2>
                <p style="color:#4b5f4c;font-size:14px;line-height:1.6;margin:0 0 20px;">{{ $intro }}</p>

                @if (count($details))
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f2f4f8;border-radius:10px;margin:0 0 20px;">
                        @foreach ($details as $label => $value)
                            <tr><td style="padding:8px 16px;font-size:14px;color:#4b5f4c;">{{ $label }}</td><td style="padding:8px 16px;font-size:14px;color:#292420;font-weight:700;text-align:right;">{{ $value }}</td></tr>
                        @endforeach
                    </table>
                @endif

                @unless ($forGuardian)
                    <p style="margin:0 0 20px;">
                        <a href="{{ route('tenant.billing') }}" style="display:inline-block;background:#567357;color:#ffffff;text-decoration:none;padding:12px 20px;border-radius:8px;font-size:14px;font-weight:600;">Open my Billing page</a>
                    </p>
                @endunless
                <p style="color:#4b5f4c;font-size:14px;line-height:1.6;margin:0;">{{ $closing }}</p>
            </td>
        </tr>
        @include('emails.partials.footer')
    </table>
</body>
</html>
