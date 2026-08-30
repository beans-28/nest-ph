<!doctype html>
<html>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f4f6f4; padding:40px 0; margin:0;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;">
        <tr>
            <td style="background:linear-gradient(90deg,#567357,#a2d9a4);padding:22px 32px;">
                <span style="color:#ffffff;font-size:20px;font-weight:700;letter-spacing:0.02em;">NEST.PH</span>
            </td>
        </tr>
        <tr>
            <td style="padding:32px;">
                <h2 style="color:#292420;margin:0 0 12px;font-size:20px;">Thanks for reaching out, {{ $inquiry->full_name }}</h2>
                <p style="color:#4b5f4c;font-size:14px;line-height:1.6;margin:0 0 16px;">
                    We've received your inquiry and someone from our team will get back to you shortly.
                </p>
                <div style="background:#eeeded;border-radius:10px;padding:16px 18px;margin:0 0 16px;">
                    <p style="margin:0 0 8px;font-size:12px;color:#7a7a7a;text-transform:uppercase;letter-spacing:0.04em;">Your message</p>
                    <p style="margin:0;font-size:14px;color:#292420;line-height:1.6;white-space:pre-line;">{{ $inquiry->message ?: '(No message provided)' }}</p>
                </div>
                <p style="color:#8a9690;font-size:12px;margin:0;">
                    If you didn't submit this inquiry, you can safely ignore this email.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
