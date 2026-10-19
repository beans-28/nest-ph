<?php

namespace Database\Seeders;

use App\Models\PartnerRequest;
use Illuminate\Database\Seeder;

/**
 * Sample "Request NEST.PH" submissions from (fictional) dormitory owners,
 * one or more per status, for the NEST.PH team dashboard at /platform.
 *
 * Safe on the live demo site: it never deletes anything and does nothing if
 * requests already exist.
 *
 *     php artisan db:seed --class=PartnerRequestDemoSeeder --force
 */
class PartnerRequestDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (PartnerRequest::exists()) {
            $this->command?->info('Owner requests already exist: nothing added.');

            return;
        }

        $requests = [
            [
                'owner_name' => 'Maricel Santos',
                'dorm_name' => 'Casa Lorenzo Ladies Dormitory',
                'email' => 'maricel.santos@example.com',
                'contact_number' => '09171234501',
                'city' => 'Sampaloc, Manila',
                'bed_count' => 48,
                'message' => "We currently track rent in a notebook and Excel. Interested in the online billing and the tenant portal. Can we see a demo on a weekend?",
                'status' => 'new',
                'notes' => null,
                'days_ago' => 1,
            ],
            [
                'owner_name' => 'Ramon dela Cruz',
                'dorm_name' => 'Kalye Uno Student Residences',
                'email' => 'ramon.delacruz@example.com',
                'contact_number' => '09281234502',
                'city' => 'Sta. Mesa, Manila',
                'bed_count' => 72,
                'message' => 'Near PUP like Pureza Station. Mostly interested in the VR tours since many of our applicants are from the provinces.',
                'status' => 'new',
                'notes' => null,
                'days_ago' => 2,
            ],
            [
                'owner_name' => 'Josephine Villanueva',
                'dorm_name' => 'JV Boarding House',
                'email' => 'jv.boardinghouse@example.com',
                'contact_number' => '09391234503',
                'city' => 'Diliman, Quezon City',
                'bed_count' => 24,
                'message' => 'Small boarding house, 6 rooms. Is this okay for a small place?',
                'status' => 'contacted',
                'notes' => 'Called on the 14th. Owner manages alone; mainly needs billing reminders. Sending pricing by email.',
                'days_ago' => 6,
            ],
            [
                'owner_name' => 'Antonio Reyes',
                'dorm_name' => 'Espana Heights Dormitory',
                'email' => 'areyes.espanaheights@example.com',
                'contact_number' => '09451234504',
                'city' => 'Sampaloc, Manila',
                'bed_count' => 120,
                'message' => 'We have 3 floors and a caretaker per floor. Need separate admin accounts with different access.',
                'status' => 'demo_scheduled',
                'notes' => 'Demo set for Saturday 10 AM via Google Meet. Show Admin Privileges and the ticket assignment feature.',
                'days_ago' => 9,
            ],
            [
                'owner_name' => 'Grace Mendoza',
                'dorm_name' => 'Taft Commons Dorm',
                'email' => 'grace.mendoza@example.com',
                'contact_number' => '09561234505',
                'city' => 'Malate, Manila',
                'bed_count' => 60,
                'message' => 'Looking for a system before the next semester intake.',
                'status' => 'onboarded',
                'notes' => 'Rooms, rates and house rules set up. Admin accounts created. Waiting for their room photos before going live.',
                'days_ago' => 21,
            ],
            [
                'owner_name' => 'Edgar Bautista',
                'dorm_name' => 'Bautista Bedspace',
                'email' => 'edgar.bautista@example.com',
                'contact_number' => '09671234506',
                'city' => 'Pasig City',
                'bed_count' => 8,
                'message' => 'Just checking the price.',
                'status' => 'declined',
                'notes' => 'Only 8 beds and the owner prefers to keep using GCash chat. Not a fit for now; follow up next year.',
                'days_ago' => 15,
            ],
        ];

        foreach ($requests as $data) {
            $sentAt = now()->subDays($data['days_ago'])->setTime(rand(8, 20), rand(0, 59));
            unset($data['days_ago']);

            PartnerRequest::forceCreate($data + [
                'dpa_consent' => true,
                'created_at' => $sentAt,
                'updated_at' => $sentAt,
            ]);
        }

        $this->command?->info('Added ' . count($requests) . ' sample owner requests.');
    }
}
