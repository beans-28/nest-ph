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
                <h2 style="color:#292420;margin:0 0 12px;font-size:20px;">Congratulations, {{ $application->full_name }}!</h2>
                <p style="color:#4b5f4c;font-size:14px;line-height:1.6;margin:0 0 20px;">
                    Your application for occupancy has been approved. A tenant account has been created for you.
                </p>

                <div style="background:#eeeded;border-radius:10px;padding:16px 18px;margin:0 0 20px;">
                    <p style="margin:0 0 8px;font-size:12px;color:#7a7a7a;text-transform:uppercase;letter-spacing:0.04em;">Your login details</p>
                    <p style="margin:0 0 4px;font-size:14px;color:#292420;"><strong>Email:</strong> {{ $email }}</p>
                    @if($temporaryPassword)
                        <p style="margin:0;font-size:14px;color:#292420;"><strong>Temporary Password:</strong> {{ $temporaryPassword }}</p>
                    @else
                        <p style="margin:0;font-size:13px;color:#6b6b6b;">Use your existing NEST.PH account password to log in.</p>
                    @endif
                </div>

                <p style="color:#4b5f4c;font-size:14px;line-height:1.6;margin:0 0 20px;">
                    Please log in and proceed to the <strong>Billing</strong> section to pay your move-in fees
                    (security deposit + 1 month advance rent). Your account will be fully activated once payment
                    is verified.
                </p>

                <p style="color:#8a9690;font-size:12px;margin:0;">
                    For your security, please change your password after logging in for the first time.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
