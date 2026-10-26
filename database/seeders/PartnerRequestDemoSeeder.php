<?php

namespace Database\Seeders;

use App\Models\PartnerRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Sample "Request NEST.PH" submissions from (fictional) dormitory owners,
 * one or more per status, each with a history of team notes, for the
 * NEST.PH team dashboard at /platform.
 *
 * Safe on the live demo site: it never deletes anything. Requests that
 * already exist are kept; only the ones missing are added, and notes are
 * added only to requests that have none yet.
 *
 *     php artisan db:seed --class=PartnerRequestDemoSeeder --force
 */
class PartnerRequestDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Notes are credited to the first NEST.PH team account, if there is one.
        $platformRoleId = DB::table('roles')->where('role_name', 'platform_admin')->value('id');
        $authorId = $platformRoleId ? DB::table('users')->where('role_id', $platformRoleId)->orderBy('id')->value('id') : null;

        // notes: [days after the request was sent, hour, note]
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
                'days_ago' => 1,
                'notes' => [
                    [0, 16, 'Tried calling, no answer. Will try again tomorrow morning.'],
                ],
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
                'days_ago' => 2,
                'notes' => [],
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
                'days_ago' => 6,
                'notes' => [
                    [1, 10, 'Called. Owner manages the place alone; mainly needs billing reminders and a record of who has paid.'],
                    [1, 15, 'Sent pricing and the tenant manual by email.'],
                    [4, 9, 'Owner replied: will decide after talking to her sister, who co-owns the building. Follow up next week.'],
                ],
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
                'days_ago' => 9,
                'notes' => [
                    [1, 11, 'Called. Three caretakers, one per floor; each should only handle tickets and payments.'],
                    [2, 14, 'Demo set for Saturday 10 AM via Google Meet.'],
                    [2, 14, 'For the demo: show Admin Privileges and the automatic assignment of emergency tickets.'],
                ],
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
                'days_ago' => 21,
                'notes' => [
                    [1, 9, 'Called. Wants to go live before the next semester intake.'],
                    [3, 13, 'Demo done. Owner liked the move-in payment flow and the delinquency reminders.'],
                    [8, 10, 'Rooms, rates and house rules set up. Admin accounts created.'],
                    [12, 16, 'Waiting for their 360 room photos before going live.'],
                ],
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
                'days_ago' => 15,
                'notes' => [
                    [1, 17, 'Called. Only 8 beds; the owner prefers to keep collecting through GCash chat.'],
                    [2, 10, 'Not a fit for now. Follow up next year.'],
                ],
            ],
        ];

        $addedRequests = 0;
        $addedNotes = 0;

        foreach ($requests as $data) {
            $notes = $data['notes'];
            $sentAt = now()->subDays($data['days_ago'])->setTime(rand(8, 12), rand(0, 59));
            unset($data['notes'], $data['days_ago']);

            $request = PartnerRequest::where('email', $data['email'])->first();
            if (! $request) {
                $request = PartnerRequest::forceCreate($data + [
                    'dpa_consent' => true,
                    'created_at' => $sentAt,
                    'updated_at' => $sentAt,
                ]);
                $addedRequests++;
            }

            if ($request->teamNotes()->exists()) {
                continue;
            }

            foreach ($notes as $i => [$daysAfter, $hour, $body]) {
                $at = $request->created_at->copy()->addDays($daysAfter)->setTime($hour, 5 + $i * 7);
                $request->teamNotes()->forceCreate([
                    'user_id' => $authorId,
                    'body' => $body,
                    'created_at' => min($at, now()),
                    'updated_at' => min($at, now()),
                ]);
                $addedNotes++;
            }
        }

        $this->command?->info("Added {$addedRequests} sample owner requests and {$addedNotes} team notes.");
    }
}
