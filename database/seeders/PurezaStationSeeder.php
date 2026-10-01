<?php

namespace Database\Seeders;

use App\Models\DormitoryCharge;
use App\Models\DormitoryHouseRule;
use App\Models\DormitoryProfile;
use App\Models\PaymentMethod;
use App\Models\RoomType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Loads Pureza Station Dormitory's real terms from its signed documents
 * (Tenant Agreement, Rules and Regulations v2, Payments and Fees Schedule
 * v1, effective September 30, 2026) into the Dormitory Profile.
 *
 * Run once:  php artisan db:seed --class=PurezaStationSeeder
 *
 * Safe to run again: room types, charges and payment methods are matched by
 * name and updated, not duplicated. HOUSE RULES ARE REPLACED with the 25
 * rules from the Rules and Regulations.
 *
 * Not in the documents, so left for the dorm to fill in on the Dormitory
 * Profile page: the authorized representative's name and position, whether
 * water / electricity / Wi-Fi are included, and whether a mid-month
 * move-in is prorated (the printed Schedule leaves those boxes unticked).
 */
class PurezaStationSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->profile();
            $this->roomTypes();
            $this->charges();
            $this->houseRules();
            $this->paymentMethods();
        });
    }

    private function profile(): void
    {
        $profile = DormitoryProfile::current();

        $profile->fill([
            'dorm_name' => 'Pureza Station Dormitory',
            'address' => '329 C De Dios, Brgy. 632, Sta. Mesa, Manila',
            'contact_email' => 'dormitorypurezastation@gmail.com',
            'facebook_page_name' => 'Pureza Station Dormitory',
            'facebook_url' => 'https://www.facebook.com/pureza.dom',
            'website_url' => 'https://thenestphils.purezastationdormitory.com',

            'rent_due_day' => 1,
            'grace_period_days' => 3,
            'late_penalty_percent' => 10,
            'minimum_stay_months' => 3,
            'move_out_notice_days' => 14,     // two (2) weeks
            'extension_notice_days' => 30,    // one (1) month
            'deposit_refund_days' => 21,
            'reservation_validity_days' => 30, // one (1) month
            'short_term_rate' => 4500,
            'transient_rate' => 300,

            'rules_version' => '2',
            'fees_version' => '1',
            'documents_effective_date' => '2026-09-30',
        ]);

        if (blank($profile->description)) {
            $profile->description = 'For students and young professionals since 1996.';
        }

        $profile->save();
    }

    private function roomTypes(): void
    {
        $types = [
            ['name' => 'Solo fan room', 'location' => 'Ground floor', 'pricing_mode' => 'per_room', 'monthly_rate' => 4500, 'min_capacity' => 1, 'max_capacity' => 1, 'has_aircon' => false],
            ['name' => 'Room with AC, 4 persons', 'location' => 'Ground floor', 'pricing_mode' => 'per_bed', 'monthly_rate' => 4500, 'min_capacity' => 4, 'max_capacity' => 4, 'has_aircon' => true],
            ['name' => 'Room with AC, 4 persons', 'location' => '2nd to 5th floor', 'pricing_mode' => 'per_bed', 'monthly_rate' => 4200, 'min_capacity' => 4, 'max_capacity' => 4, 'has_aircon' => true],
            ['name' => 'Room with AC, 6 persons', 'location' => '2nd to 5th floor', 'pricing_mode' => 'per_bed', 'monthly_rate' => 4000, 'min_capacity' => 6, 'max_capacity' => 6, 'has_aircon' => true],
            ['name' => 'Room with AC, 10–16 persons', 'location' => '2nd to 5th floor', 'pricing_mode' => 'per_bed', 'monthly_rate' => 3800, 'min_capacity' => 10, 'max_capacity' => 16, 'has_aircon' => true],
        ];

        foreach ($types as $i => $type) {
            $roomType = RoomType::updateOrCreate(
                ['name' => $type['name'], 'location' => $type['location']],
                $type + ['sort_order' => $i + 1]
            );

            // Rooms already using this type pick up the price.
            $roomType->rooms()->get()->each->syncFromRoomType();
        }
    }

    private function charges(): void
    {
        $charges = [
            ['name' => 'Lost or unreturned key (key duplication)', 'amount' => 50, 'amount_note' => null, 'when_applies' => 'Upon loss or check-out'],
            ['name' => 'Possession or use of hazardous items (Rules, item 9)', 'amount' => 500, 'amount_note' => null, 'when_applies' => 'Per violation'],
            ['name' => 'Damage to dormitory property', 'amount' => null, 'amount_note' => 'Reasonable repair or replacement cost', 'when_applies' => 'Upon assessment'],
            ['name' => 'Approved high-power appliance', 'amount' => null, 'amount_note' => 'per month (amount to be set)', 'when_applies' => 'If approved'],
            ['name' => 'Late check-out after 2:00 PM', 'amount' => null, 'amount_note' => 'Amount to be set', 'when_applies' => 'If applicable'],
        ];

        foreach ($charges as $i => $charge) {
            DormitoryCharge::updateOrCreate(['name' => $charge['name']], $charge + ['sort_order' => $i + 1]);
        }
    }

    private function houseRules(): void
    {
        $sections = [
            'A. Conduct and Respect' => [
                'Treat all tenants, staff, and visitors with respect and consideration. Harassment, discrimination, and bullying are not tolerated.',
                'Quiet hours are 10:00 PM to 6:00 AM. At all other times, tenants must still avoid unreasonable noise that disturbs others.',
                'Curfew is from 11:00 PM to 4:00 AM. Tenants should be inside the dormitory during curfew hours. A tenant who must be out late (for example, because of night work or a class schedule) or expects to return during curfew must inform the management in advance. Emergencies are excepted, and the tenant should inform the management as soon as possible.',
                'Complaints and suggestions to improve the dormitory are welcome and may be sent to the management or through NEST.PH.',
            ],
            'B. Visitors and Room Access' => [
                'Only registered tenants may enter the rooms. Visitors are received at the receiving area only. Overnight guests are not allowed.',
                'Tenants are responsible for their visitors and for any loss or damage they cause.',
                'Management may enter rooms for inspection, maintenance, and safety checks as stated in Section 7 of the Agreement.',
            ],
            'C. Safety and Prohibited Items' => [
                'Alcoholic beverages, smoking, and vaping are not allowed on the premises.',
                'Hazardous items such as gas, cooking stoves, flammable fuels, and firearms are strictly prohibited. A tenant found using or keeping them may be fined the amount listed in the Payments and Fees Schedule (currently ₱500), and the matter may be reported to the proper authorities where required by law.',
                'Drugs and other illegal substances are strictly prohibited. Possession may be reported to the proper authorities.',
                'Pets are not allowed on the premises.',
                'Do not tamper with fire alarms, extinguishers, or CCTV equipment, and do not block hallways, stairs, or exits. Follow the evacuation plan and staff instructions during a fire, earthquake, or other emergency.',
                'Do not bring or use appliances that draw high power (for example heaters, irons, or cooking appliances) unless the management allows them. Approved appliances may carry an electricity charge listed in the Payments and Fees Schedule.',
                'Cameras (CCTV) may be installed in common areas for safety and security. They are not placed in bedrooms or bathrooms. How footage is used and kept is explained in the Privacy Notice.',
            ],
            'D. Care of the Premises' => [
                'Keep common areas clean and tidy after use and dispose of garbage in the proper place. Washing clothes in the dormitory is not allowed. A laundry service is available outside the dormitory.',
                'Use the air-conditioner only from 10:00 PM to 5:00 AM (aircon schedule), and keep doors and windows closed while it is running.',
                'When leaving the room, turn off all faucets, showers, lights, air-conditioners, and other devices. Close and lock the door.',
                'Do not move, remove, or swap beds, furniture, or rooms without the management’s approval. Do not transfer or sublet your bed to another person.',
                'Report any damage, defect, or unsafe condition promptly to the maintenance staff or through NEST.PH.',
                'The tenant pays the reasonable cost of repair or replacement for loss or damage caused by the tenant, the tenant’s visitors, or anyone the tenant is responsible for, as stated in Section 6.3 of the Agreement. Charges follow the Payments and Fees Schedule.',
                'If a key is lost or damaged, the tenant pays the key-duplication fee listed in the Payments and Fees Schedule (currently ₱50).',
            ],
            'E. Liability' => [
                'Tenants must take reasonable care of their belongings and their own safety. The management’s responsibility for loss, damage, or injury is stated in Section 11 of the Agreement. Nothing in these Rules limits any liability that the law does not allow to be limited.',
            ],
            'F. Breach and Disciplinary Steps' => [
                'For a breach of these Rules, the management will usually follow these steps: (a) verbal reminder; (b) written warning; (c) fine, where one is listed in the Payments and Fees Schedule; and (d) termination under Section 13.2 of the Agreement.',
                'The management may skip steps for serious violations, such as illegal drugs, weapons, violence, or conduct that threatens the safety of others. Termination is always subject to the Agreement and Philippine law. The tenant may explain his or her side before a fine or termination is finalized.',
                'The management may update these Rules for reasonable administrative, operational, safety, or security reasons, with advance notice to tenants as stated in Section 5.3 of the Agreement.',
            ],
        ];

        DormitoryHouseRule::query()->delete();

        $order = 0;
        foreach ($sections as $section => $rules) {
            foreach ($rules as $rule) {
                DormitoryHouseRule::create([
                    'section' => $section,
                    'rule_text' => $rule,
                    'sort_order' => ++$order,
                ]);
            }
        }
    }

    private function paymentMethods(): void
    {
        $methods = [
            ['type' => 'ewallet', 'name' => 'GCash', 'account_name' => 'Patricia Joy N.', 'account_number' => '09178932970'],
            ['type' => 'bank', 'name' => 'BDO', 'account_name' => 'Patricia Joy Han', 'account_number' => '005548032974'],
        ];

        foreach ($methods as $method) {
            $existing = PaymentMethod::whereRaw('LOWER(name) = ?', [strtolower($method['name'])])->first();

            if ($existing) {
                $existing->update($method);
            } else {
                PaymentMethod::create($method + ['sort_order' => (int) PaymentMethod::max('sort_order') + 1]);
            }
        }

        // The cash row comes from a migration; give it the Schedule's wording.
        PaymentMethod::where('type', 'cash')->update([
            'instructions' => 'Paid to authorized dormitory staff, who record the payment in NEST.PH and issue a receipt.',
        ]);
    }
}
