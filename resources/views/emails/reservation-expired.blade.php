<!doctype html>
<html>
<body style="font-family: Arial, Helvetica, sans-serif; background:#f4f6f4; padding:40px 0; margin:0;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;">
        <tr>
            @include('emails.partials.header')
        </tr>
        <tr>
            <td style="padding:32px;">
                <h2 style="color:#292420;margin:0 0 12px;font-size:20px;">Your reservation has expired, {{ $tenant->full_name }}</h2>
                <p style="color:#4b5f4c;font-size:14px;line-height:1.6;margin:0 0 20px;">
                    You paid half of your move-in fee, but the remaining balance wasn't paid within one month
                    of your payment. As stated in the Payments and Fees Schedule (Section 3.2), a reservation is
                    valid for one month, so your bedspace has been released.
                </p>

                <div style="background:#eeeded;border-radius:10px;padding:16px 18px;margin:0 0 20px;">
                    <p style="margin:0 0 4px;font-size:14px;color:#292420;"><strong>Move-in fee:</strong> &#8369;{{ number_format($bill->total_amount, 2) }}</p>
                    <p style="margin:0 0 4px;font-size:14px;color:#292420;"><strong>Amount paid:</strong> &#8369;{{ number_format($bill->total_amount - $bill->remainingBalance(), 2) }}</p>
                    <p style="margin:0;font-size:14px;color:#292420;"><strong>Reservation deadline:</strong> {{ optional($bill->reservationDeadline())->format('F j, Y') }}</p>
                </div>

                <p style="color:#4b5f4c;font-size:14px;line-height:1.6;margin:0;">
                    For questions about the amount you already paid, or if you'd still like to stay with us,
                    please contact the dormitory administrator.
                </p>
            </td>
        </tr>
        @include('emails.partials.footer')
    </table>
</body>
</html>
