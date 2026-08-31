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
                <h2 style="color:#292420;margin:0 0 12px;font-size:20px;">Payment verified, {{ $tenant->full_name }}!</h2>
                <p style="color:#4b5f4c;font-size:14px;line-height:1.6;margin:0 0 20px;">
                    Your move-in fee has been verified by our administrator. Your account is now fully active,
                    and you have complete access to your tenant portal — billing, maintenance requests, and more.
                </p>

                <div style="background:#eeeded;border-radius:10px;padding:16px 18px;margin:0 0 20px;">
                    <p style="margin:0 0 8px;font-size:12px;color:#7a7a7a;text-transform:uppercase;letter-spacing:0.04em;">Move-In Permit</p>
                    <p style="margin:0 0 4px;font-size:14px;color:#292420;"><strong>Tenant:</strong> {{ $tenant->full_name }}</p>
                    @if($contract)
                        <p style="margin:0 0 4px;font-size:14px;color:#292420;"><strong>Move-in date:</strong> {{ optional($contract->start_date)->format('F j, Y') }}</p>
                    @endif
                    <p style="margin:0;font-size:14px;color:#292420;"><strong>Issued:</strong> {{ now()->format('F j, Y') }}</p>
                </div>

                <p style="color:#4b5f4c;font-size:14px;line-height:1.6;margin:0;">
                    Welcome to Pureza Station Dormitory — we're glad to have you!
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
