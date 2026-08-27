<?php

namespace Database\Seeders;

use App\Models\DormitoryProfile;
use Illuminate\Database\Seeder;

class DormitoryProfileSeeder extends Seeder
{
    /**
     * Seeds the single dormitory profile row. Content pulled from the
     * DL CONTRACT frame in the Figma file — replace with the dorm's real
     * final copy once BAGUI confirms it with the client.
     */
    public function run(): void
    {
        DormitoryProfile::updateOrCreate(
            ['id' => 1],
            [
                'dorm_name' => 'NEST.PH',
                'description' => 'A safe, comfortable, and affordable place to live, study, and grow.',
                'address' => 'Pureza Station, Manila',
                'contact_number' => '0917-893-2970',
                'contact_email' => 'dormitorypurezastation@gmail.com',

                'payments_and_fees' => <<<'TEXT'
Rent may be paid in cash, GCash, or bank deposit (BDO).

Tenancy is subject to a three-month minimum. Tenants must provide the start
and end date of their stay upon registration.

Upon registration, new tenants pay a reservation fee composed of a security
deposit (one month, refundable for a 3-month contract) and one month advance
rent. The reservation fee is non-refundable if the tenant cancels or checks
out earlier than the three-month minimum. The deposit is returned within
2–3 weeks after the check-out date.

Rent is due every 1st day of the month. For GCash or bank deposit, payment
confirmation must be sent to the dormitory's official contact channels.

Tenants are granted a 3-day grace period for late rent payments. Beyond the
grace period, a 10% penalty fee applies. Failure to pay within one month
results in a notice of eviction for non-payment.

Tenants wishing to extend their stay must give at least 1 month notice.
Move-out requires at least 2 weeks notice; move-out schedule is end of month.
TEXT,

                'house_rules' => <<<'TEXT'
Alcoholic beverages, smoking, and vaping are not allowed on dormitory premises.

Washing of clothes is not allowed; a laundry service is available outside.

Tenants are responsible for keeping common areas clean after use, and must
promptly report any damages or issues to maintenance staff.

Tenants must pay for any loss or damage to dormitory property caused by
themselves or their guests, at the cost of the damage (minimum ₱500).

Only registered tenants may enter the rooms. Visitors may be entertained at
the receiving area.

Hazardous goods (gas, cooking stoves, flammable fuels, firearms) are strictly
prohibited; violation carries a ₱500 fine and may be reported to authorities.
Drugs and illegal substances are strictly prohibited and will be reported.

Silence should be observed at all times out of consideration for other tenants.
Treat fellow tenants and staff with respect — harassment, discrimination, or
bullying will not be tolerated.

Management is not responsible for losses or injuries occurring on the premises.
Tenants should exercise care and diligence at all times.

Doors and windows must be closed when using the air-conditioner. When leaving,
turn off all faucets, showers, lights, air conditioners, and appliances, and
lock the door. Lost or damaged keys cost ₱50 to replace.

A strict NO PETS policy is enforced.

Curfew hours: 11PM – 4AM. Aircon schedule: 10PM – 5AM.
TEXT,

                'checkout_procedures' => <<<'TEXT'
Advanced notice: Residents planning to check out must give written notice at
least two weeks before their intended departure date.

Room inspection: A staff member will inspect the room/bed before check-out to
assess damages or cleanliness issues. Rooms should be clean before inspection.

Damages and repairs: Residents are responsible for damage beyond normal wear
and tear, and will be charged for repairs or replacements.

Furniture and equipment: All dormitory-provided furniture and equipment must
be present and in good condition. Missing or damaged items incur charges.

Cleanliness: Rooms must be left in move-in condition, with all personal
belongings removed and shared areas cleaned.

Trash disposal: Dispose of all trash and recyclables in designated bins.

Key return: Room keys must be returned upon check-out. Failure to return keys
may result in a fine.

Check-out time: Residents must vacate by 2:00 PM on the check-out date.

Final settlement: After inspection, the security deposit is returned minus any
deductions for damages or outstanding charges, within two weeks of check-out.
TEXT,
            ]
        );
    }
}
