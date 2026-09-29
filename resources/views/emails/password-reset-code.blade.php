<!doctype html>
<html>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f4f6f4; padding:40px 0; margin:0;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;">
        <tr>
            @include('emails.partials.header')
        </tr>
        <tr>
            <td style="padding:32px;">
                <h2 style="color:#292420;margin:0 0 12px;font-size:20px;">Password Reset Code</h2>
                <p style="color:#4b5f4c;font-size:14px;line-height:1.6;margin:0 0 8px;">
                    Use the code below to reset your {{ $brandDormName }} tenant portal password. This code expires in 10 minutes.
                </p>
                <div style="text-align:center;margin:28px 0;">
                    <span style="display:inline-block;background:#eeeded;color:#567357;font-size:32px;font-weight:700;letter-spacing:10px;padding:16px 24px;border-radius:10px;">
                        {{ $code }}
                    </span>
                </div>
                <p style="color:#8a9690;font-size:12px;margin:0;">
                    If you didn't request this, you can safely ignore this email.
                </p>
            </td>
        </tr>
        @include('emails.partials.footer')
    </table>
</body>
</html>
