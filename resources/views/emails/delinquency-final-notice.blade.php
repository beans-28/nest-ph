<!doctype html>
<html>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f4f6f4; padding:40px 0; margin:0;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;">
        <tr>
            @include('emails.partials.header')
        </tr>
        <tr>
            <td style="padding:32px;">
                <h2 style="color:#292420;margin:0 0 12px;font-size:20px;">Your balance is still unpaid, {{ $tenant->full_name }}</h2>
                <p style="color:#4b5f4c;font-size:14px;line-height:1.6;margin:0 0 20px;">
                    Your account remains overdue and your portal access is restricted. Please settle your
                    balance as soon as possible to avoid a formal demand letter and further action.
                </p>

                <div style="background:#eeeded;border-radius:10px;padding:16px 18px;margin:0 0 20px;">
                    <p style="margin:0 0 4px;font-size:14px;color:#292420;"><strong>Amount due:</strong> &#8369;{{ number_format($bill->remainingBalance(), 2) }}</p>
                    @if ((float) $bill->penalty_amount > 0)
                        <p style="margin:0 0 4px;font-size:14px;color:#292420;"><strong>Includes penalty:</strong> &#8369;{{ number_format((float) $bill->penalty_amount, 2) }}</p>
                    @endif
                    <p style="margin:0;font-size:14px;color:#292420;"><strong>Due date:</strong> {{ $bill->due_date->format('F j, Y') }}</p>
                </div>

                <p style="color:#4b5f4c;font-size:14px;line-height:1.6;margin:0;">
                    You can pay through the Billing page of the tenant portal. If you have already paid,
                    please contact the dormitory administrator.
                </p>
            </td>
        </tr>
        @include('emails.partials.footer')
    </table>
</body>
</html>
