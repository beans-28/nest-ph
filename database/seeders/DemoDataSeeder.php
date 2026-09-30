<?php

namespace Database\Seeders;

use App\Models\Bed;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Capstone defense demo data.
 *
 * WIPES every tenant, admin, application, bill, payment, ticket, review,
 * announcement and inquiry, then fills the system with realistic Filipino
 * dummy data covering every scenario the system supports. The only things
 * kept are: the owner account, the dormitory profile, payment methods,
 * amenities/house rules, and the existing rooms (so their VR tours survive).
 *
 * Run with:  php artisan db:seed --class=DemoDataSeeder
 *
 * All dates are relative to the day it's run, so re-run it the day before
 * the defense and "3 days overdue" will still mean 3 days overdue.
 *
 * Every account's password: Password123!
 */
class DemoDataSeeder extends Seeder
{
    private const PASSWORD = 'Password123!';

    private const OWNER_EMAIL = 'owner@nestph.test';

    /**
     * The SMS gateway (TextBee) is LIVE. The escalation engine automatically
     * texts overdue tenants and their emergency contacts, so those tenants use
     * this number instead of a made-up one -- any text goes to the team's own
     * phone, never a stranger's. Change it to whichever phone you'll have at
     * the defense.
     */
    private const DEMO_SMS_NUMBER = '09212408565';

    /**
     * Walkthrough script: Ben's texts (reminders, portal restriction,
     * emergency contact, eviction notice) go to the phone Shayne holds.
     * TODO: replace with Shayne's actual number -- until then it uses
     * DEMO_SMS_NUMBER so nothing is ever sent to a stranger.
     */
    private const BEN_SMS_NUMBER = '09212408565';

    /**
     * Ana gets a review-request SMS when the owner deactivates her in Part 4,
     * so her number must also be a team phone.
     */
    private const ANA_SMS_NUMBER = self::DEMO_SMS_NUMBER;

    private Carbon $today;

    private int $ownerId;

    private array $admins = [];   // key => user id

    private array $beds = [];     // '101-1' => bed id

    private array $rooms = [];    // '101' => room row

    private array $files = [];    // reusable uploaded files already in storage

    private int $refCounter = 1000;

    public function run(): void
    {
        // This seeder empties tables before filling them with demo data.
        // On the live site that would wipe real tenants and payments.
        if (app()->environment('production')) {
            throw new \RuntimeException('DemoDataSeeder refuses to run in production: it deletes existing data.');
        }

        $this->today = now()->startOfDay();
        $this->pickExistingFiles();

        // No wrapping transaction: MySQL's TRUNCATE can't be rolled back anyway.
        $this->wipe();
        $this->seedAdmins();
        $this->seedFloorsRoomsBeds();
        $this->seedTenants();
        $this->seedFormerTenants();
        $this->seedPendingApplications();
        $this->seedMonthlyExpenses();
        $this->seedInquiries();
        $this->seedAnnouncements();
        $this->prepareVrDemo();
        $this->syncRoomStatuses();

        $this->command?->info('Demo data seeded. Every password is ' . self::PASSWORD);
    }

    /* ------------------------------------------------------------------ */
    /*  1. Wipe                                                            */
    /* ------------------------------------------------------------------ */

    private function wipe(): void
    {
        $owner = DB::table('users')->where('email', self::OWNER_EMAIL)->first();

        if (! $owner) {
            throw new \RuntimeException('Owner account ' . self::OWNER_EMAIL . ' not found -- aborting so nothing is deleted.');
        }

        $this->ownerId = $owner->id;

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ([
            'announcement_comments', 'announcements', 'ticket_replies', 'maintenance_tickets',
            'reviews', 'penalty_audit_logs', 'penalties', 'damages', 'escalation_logs',
            'payments', 'billing_statements', 'lease_contracts', 'applications', 'inquiries',
            'tenants', 'beds', 'admin_access_logs', 'admin_login_sessions', 'sessions',
            'password_reset_codes', 'password_reset_tokens', 'personal_access_tokens',
            'deposit_refunds', 'tenant_notifications', 'monthly_expenses',
        ] as $table) {
            DB::table($table)->truncate();
        }

        DB::table('admin_privileges')->where('user_id', '!=', $this->ownerId)->delete();
        DB::table('users')->where('id', '!=', $this->ownerId)->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /* ------------------------------------------------------------------ */
    /*  2. Owner + admins                                                  */
    /* ------------------------------------------------------------------ */

    private function seedAdmins(): void
    {
        $adminRole = DB::table('roles')->where('role_name', 'admin')->value('id');
        $password = Hash::make(self::PASSWORD);

        DB::table('users')->where('id', $this->ownerId)->update([
            'name' => 'Teresita Mendoza',
            'password' => $password,
            'role_id' => $adminRole,
            'is_active' => true,
            'updated_at' => now(),
        ]);

        // Owner always holds every privilege (manage_users is what makes them Owner).
        $all = ['manage_tenants', 'manage_rooms', 'manage_contracts', 'manage_billing', 'manage_users', 'view_reports'];
        foreach ($all as $p) {
            DB::table('admin_privileges')->updateOrInsert(
                ['user_id' => $this->ownerId, 'privilege_name' => $p],
                ['granted_at' => $this->today->copy()->subMonths(8)]
            );
        }
        $this->admins['owner'] = $this->ownerId;

        $staff = [
            // key, name, email, active, privileges, note
            ['kristine', 'Kristine Joy Bautista', 'kristine.bautista@nestph.test', true,
                ['manage_tenants', 'manage_rooms', 'manage_contracts', 'manage_billing', 'view_reports'],
                'Dorm manager -- everything except managing other admins.'],
            ['mark', 'Mark Anthony Villanueva', 'mark.villanueva@nestph.test', true,
                ['manage_billing', 'view_reports'],
                'Cashier -- billing and reports only.'],
            ['jerome', 'Jerome Castillo', 'jerome.castillo@nestph.test', true,
                ['manage_tenants', 'manage_rooms'],
                'Front desk / maintenance -- tenants and rooms only.'],
            ['lea', 'Lea Mae Fernandez', 'lea.fernandez@nestph.test', false,
                [],
                'Former staff -- access revoked.'],
        ];

        foreach ($staff as [$key, $name, $email, $active, $privs]) {
            $created = $this->today->copy()->subMonths(6)->addDays(count($this->admins) * 9);
            $id = DB::table('users')->insertGetId([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'role_id' => $adminRole,
                'is_active' => $active,
                'created_at' => $created,
                'updated_at' => $created,
            ]);
            $this->admins[$key] = $id;

            DB::table('admin_access_logs')->insert([
                'user_id' => $id,
                'performed_by' => $this->ownerId,
                'action' => 'granted',
                'note' => 'Admin access granted: ' . implode(', ', $privs ?: ['manage_tenants', 'view_reports']),
                'created_at' => $created,
            ]);

            foreach ($privs as $p) {
                DB::table('admin_privileges')->insert([
                    'user_id' => $id,
                    'granted_by' => $this->ownerId,
                    'privilege_name' => $p,
                    'granted_at' => $created,
                ]);
            }
        }

        // Audit trail examples: a privilege change and a revocation.
        DB::table('admin_access_logs')->insert([
            ['user_id' => $this->admins['mark'], 'performed_by' => $this->ownerId, 'action' => 'privileges_updated',
                'note' => 'Privileges updated: manage_billing, view_reports', 'created_at' => $this->today->copy()->subDays(40)],
            ['user_id' => $this->admins['lea'], 'performed_by' => $this->ownerId, 'action' => 'revoked',
                'note' => 'Admin access revoked. Reason: No longer employed at the dormitory.', 'created_at' => $this->today->copy()->subDays(21)],
        ]);

        // Login tracker history for the dashboard.
        $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36';
        foreach (['owner' => [1, 2, 4, 6, 9, 13], 'kristine' => [0, 1, 2, 3, 5, 7, 8], 'mark' => [1, 3, 6, 10], 'jerome' => [0, 2, 4, 11]] as $key => $daysAgo) {
            foreach ($daysAgo as $d) {
                $in = $this->today->copy()->subDays($d)->setTime(8 + ($d % 5), 10 + $d * 3);
                DB::table('admin_login_sessions')->insert([
                    'user_id' => $this->admins[$key],
                    'session_id' => bin2hex(random_bytes(20)),
                    'ip_address' => '192.168.1.' . (20 + $d),
                    'user_agent' => $ua,
                    'logged_in_at' => $in,
                    'logged_out_at' => $d === 0 ? null : $in->copy()->addHours(3)->addMinutes(12),
                ]);
            }
        }
    }

    /* ------------------------------------------------------------------ */
    /*  3. Floors, rooms, beds                                             */
    /* ------------------------------------------------------------------ */

    private function seedFloorsRoomsBeds(): void
    {
        $floorSpecs = [
            1 => ['Ground Floor', 'Lobby, receiving area and study hall. Mixed rooms near the entrance.'],
            2 => ['Second Floor', 'Shared rooms and quiet solo units for working tenants.'],
            3 => ['Third Floor', 'Newly renovated rooms with balcony access.'],
        ];

        // Reuse the existing floor rows (rooms with VR tours point at them) and add what's missing.
        $existingFloors = DB::table('floors')->orderBy('id')->pluck('id')->all();
        $floorIds = [];
        foreach ($floorSpecs as $num => [$name, $desc]) {
            $data = ['floor_name' => $name, 'floor_number' => $num, 'description' => $desc, 'updated_at' => now()];
            if ($id = array_shift($existingFloors)) {
                DB::table('floors')->where('id', $id)->update($data);
            } else {
                $id = DB::table('floors')->insertGetId($data + ['created_at' => now()]);
            }
            $floorIds[$num] = $id;
        }
        // Any extra old floors: move their rooms off first, then remove them.
        foreach ($existingFloors as $extra) {
            DB::table('rooms')->where('floor_id', $extra)->update(['floor_id' => $floorIds[1]]);
            DB::table('floors')->where('id', $extra)->delete();
        }

        // room_no, floor, type, beds, whole-room rate, utilities, wifi, amenities, vr caption
        $roomSpecs = [
            ['101', 1, 'Quad Sharing', 4, 14000, 2000, 1000, ['Air-conditioned', 'Study table', 'Cabinet per bed', 'Shared CR'], 'Bright quad room beside the study hall'],
            ['102', 1, 'Double Sharing', 2, 9000, 1200, 600, ['Air-conditioned', 'Study table', 'Private CR'], 'Cozy double room with private CR'],
            ['103', 1, 'Quad Sharing', 4, 13000, 2000, 1000, ['Air-conditioned', 'Study table', 'Shared CR'], 'Spacious quad room with computer corner'],
            ['104', 1, 'Solo Room', 1, 7500, 800, 500, ['Air-conditioned', 'Private CR', 'Mini fridge'], null],
            ['201', 2, '6-Bed Dormitory', 6, 15000, 2400, 1200, ['Air-conditioned', 'Double-deck beds', 'Lockers', 'Shared CR'], null],
            ['202', 2, 'Double Sharing', 2, 9500, 1200, 600, ['Air-conditioned', 'Study table', 'Private CR'], null],
            ['203', 2, 'Quad Sharing', 4, 14000, 2000, 1000, ['Air-conditioned', 'Study table', 'Cabinet per bed'], null],
            ['204', 2, 'Solo Room', 1, 8000, 800, 500, ['Air-conditioned', 'Private CR', 'Balcony'], null],
            ['301', 3, 'Quad Sharing', 4, 13600, 2000, 1000, ['Air-conditioned', 'Study table', 'Balcony access'], null],
            ['302', 3, 'Double Sharing', 2, 9000, 1200, 600, ['Air-conditioned', 'Private CR'], null],
            ['303', 3, '6-Bed Dormitory', 6, 15600, 2400, 1200, ['Air-conditioned', 'Double-deck beds', 'Lockers'], null],
            ['304', 3, 'Solo Room', 1, 7800, 800, 500, ['Air-conditioned', 'Private CR', 'Work desk'], null],
        ];

        // The first existing rooms (the ones with VR tours) become 101, 102, 103, 201.
        $existingRooms = DB::table('rooms')->orderBy('id')->pluck('id')->all();
        $reuseFor = ['101', '102', '103', '201'];
        $keepIds = [];

        foreach ($roomSpecs as [$no, $floor, $type, $bedCount, $rate, $util, $wifi, $amen, $caption]) {
            $data = [
                'floor_id' => $floorIds[$floor],
                'room_no' => $no,
                'room_type' => $type,
                'amenities' => json_encode($amen),
                'monthly_rate' => $rate,
                'monthly_utility_cost' => $util,
                'monthly_wifi_cost' => $wifi,
                'status' => $no === '104' ? 'maintenance' : 'available',
                'updated_at' => now(),
            ];

            $roomId = null;
            if (in_array($no, $reuseFor, true) && $existingRooms) {
                $roomId = array_shift($existingRooms);
                $hasVr = DB::table('vr_scenes')->where('room_id', $roomId)->exists();
                $data['vr_caption'] = $caption ?? DB::table('rooms')->where('id', $roomId)->value('vr_caption');
                $data['vr_visibility'] = $hasVr ? 'public' : 'draft';
                DB::table('rooms')->where('id', $roomId)->update($data);
            } else {
                $roomId = DB::table('rooms')->insertGetId($data + [
                    'vr_caption' => null,
                    'vr_visibility' => 'draft',
                    'created_at' => $this->today->copy()->subMonths(14),
                ]);
            }
            $keepIds[] = $roomId;

            $this->rooms[$no] = (object) ['id' => $roomId, 'beds' => $bedCount, 'rate' => $rate, 'util' => $util, 'wifi' => $wifi, 'type' => $type];

            for ($b = 1; $b <= $bedCount; $b++) {
                $status = 'vacant';
                if ($no === '104' || ($no === '203' && $b === 4)) {
                    $status = 'maintenance';
                }
                $this->beds["{$no}-{$b}"] = DB::table('beds')->insertGetId([
                    'room_id' => $roomId,
                    'bed_label' => "Bed {$b}",
                    'status' => $status,
                    'created_at' => $this->today->copy()->subMonths(14),
                    'updated_at' => now(),
                ]);
            }
        }

        // Any leftover old rooms (not reused above) are removed along with their VR scenes.
        $leftover = DB::table('rooms')->whereNotIn('id', $keepIds)->pluck('id');
        if ($leftover->isNotEmpty()) {
            $scenes = DB::table('vr_scenes')->whereIn('room_id', $leftover)->pluck('id');
            DB::table('vr_hotspots')->whereIn('vr_scene_id', $scenes)->orWhereIn('target_scene_id', $scenes)->delete();
            DB::table('vr_scenes')->whereIn('room_id', $leftover)->delete();
            DB::table('room_photos')->whereIn('room_id', $leftover)->delete();
            DB::table('rooms')->whereIn('id', $leftover)->delete();
        }
    }

    /* ------------------------------------------------------------------ */
    /*  4. Tenants -- one per demo scenario                                */
    /* ------------------------------------------------------------------ */

    /**
     * Scenario keys (what the LAST monthly bill looks like):
     *   paid            fully paid, nothing due
     *   unpaid          current bill issued, not yet due
     *   partial         part of the current bill paid in cash
     *   proof_pending   GCash proof uploaded, waiting for admin review
     *   proof_rejected  a proof was rejected, bill still unpaid
     *   overdue         overdue by `days` days -- escalation ladder applied
     *   pending_movein  approved, has not paid move-in fees yet
     *
     * L = how many days ago the latest billing period started
     * k = how many earlier months of (paid) billing history exist
     */
    private function tenantSpecs(): array
    {
        return [
            // ---- Room 101 (Quad) ----
            ['Maria Angelica', 'Santos', 'female', '2004-03-14', 'student', 'University of Santo Tomas', 'España Blvd., Sampaloc, Manila',
                'Brgy. San Isidro, Angono, Rizal', 'Rodelio Santos', 'Father', '101-1', 'paid', 'L' => 10, 'k' => 5,
                'review' => [5, 'Sobrang linis ng rooms and very accommodating ang staff. Malapit pa sa UST, walking distance lang. Highly recommended!']],
            ['Kimberly Anne', 'Dela Cruz', 'female', '2005-07-02', 'student', 'Far Eastern University', 'Nicanor Reyes St., Sampaloc, Manila',
                '123 Rizal St., Brgy. Poblacion, Tarlac City, Tarlac', 'Marites Dela Cruz', 'Mother', '101-2', 'unpaid', 'L' => 2, 'k' => 3],
            ['Patricia Mae', 'Gonzales', 'female', '2003-11-21', 'working_student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila',
                'Purok 4, Brgy. Maligaya, San Jose, Nueva Ecija', 'Lorna Gonzales', 'Mother', '101-3', 'proof_pending', 'L' => 4, 'k' => 2],
            ['Nicole Joy', 'Ramos', 'female', '2004-01-09', 'student', 'Technological University of the Philippines', 'Ayala Blvd., Ermita, Manila',
                'Blk 5 Lot 12, Villa Verde Subd., Dasmariñas, Cavite', 'Ernesto Ramos', 'Father', '301-4', 'paid', 'L' => 12, 'k' => 2,
                'lease' => 'expiring_soon'],

            // ---- Room 102 (Double) ----
            ['Juan Miguel', 'Reyes', 'male', '1999-05-30', 'full_time_employee', 'Accenture Philippines', 'Cyber One Bldg., Eastwood City, Quezon City',
                '45 Mabini St., Brgy. Poblacion, Lipa City, Batangas', 'Carmelita Reyes', 'Mother', '102-1', 'paid', 'L' => 15, 'k' => 6,
                'review' => [4, 'Maayos ang WiFi at tahimik sa gabi. Minsan lang medyo mahina ang tubig sa umaga pero agad naman inaayos.']],
            ['John Paul', 'Mendoza', 'male', '2004-08-17', 'student', 'Mapúa University', 'Muralla St., Intramuros, Manila',
                'Brgy. Bagong Silang, Lucena City, Quezon', 'Rosalie Mendoza', 'Mother', '303-4', 'overdue', 'L' => 14, 'k' => 3, 'days' => 9],

            // ---- Room 103 (Quad) ----
            ['Mark Joseph', 'Aquino', 'male', '2005-02-11', 'student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila',
                'Sitio Malinis, Brgy. San Roque, Antipolo City, Rizal', 'Josefina Aquino', 'Mother', '103-1', 'paid', 'L' => 5, 'k' => 1],
            ['Christian Dave', 'Torres', 'male', '2002-12-03', 'part_time_employee', 'Jollibee Foods Corp. (V. Mapa branch)', 'V. Mapa St., Sta. Mesa, Manila',
                '78 Bonifacio St., Brgy. Centro, Iriga City, Camarines Sur', 'Dante Torres', 'Father', '103-2', 'partial', 'L' => 3, 'k' => 4],
            // "Ben" in the walkthrough script: overdue at Stage 2, escalated live to Stage 6.
            ['Benjamin', 'Robles', 'male', '2004-06-25', 'student', 'National University', 'M.F. Jhocson St., Sampaloc, Manila',
                'Brgy. Santo Cristo, San Fernando, Pampanga', 'Evelyn Robles', 'Mother', '103-3', 'overdue', 'L' => 8, 'k' => 3, 'days' => 3,
                'sms' => self::BEN_SMS_NUMBER],
            ['Rafael Luis', 'Navarro', 'male', '2001-09-14', 'full_time_employee', 'BDO Unibank, Sta. Mesa Branch', 'Ramon Magsaysay Blvd., Sta. Mesa, Manila',
                '12 Aguinaldo Hwy., Brgy. Zapote, Bacoor, Cavite', 'Gloria Navarro', 'Mother', '304-1', 'paid', 'L' => 12, 'k' => 5,
                'lease' => 'expired'],

            // ---- Room 201 (6-bed) ----
            ['Angela Marie', 'Villanueva', 'female', '1998-04-19', 'full_time_employee', 'Philippine General Hospital', 'Taft Ave., Ermita, Manila',
                'Brgy. Poblacion, Tuguegarao City, Cagayan', 'Arnel Villanueva', 'Father', '201-1', 'paid', 'L' => 20, 'k' => 8, 'discount' => 250,
                'review' => [5, 'Almost a year na ako dito. Safe, may curfew, at mabait si Ma\'am Tess. Perfect para sa mga nurse na shifting.']],
            ['Jasmine Rose', 'Garcia', 'female', '2005-10-05', 'student', 'Centro Escolar University', 'Mendiola St., San Miguel, Manila',
                'Purok 2, Brgy. Talon, Las Piñas City', 'Ramil Garcia', 'Father', '201-2', 'proof_rejected', 'L' => 1, 'k' => 2],
            ['Camille Louise', 'Flores', 'female', '2004-12-28', 'student', 'Lyceum of the Philippines University', 'Muralla St., Intramuros, Manila',
                'Brgy. Mabini, Batangas City, Batangas', 'Susan Flores', 'Mother', '201-3', 'overdue', 'L' => 11, 'k' => 3, 'days' => 6, 'paused' => true],
            ['Bea Katrina', 'Pascual', 'female', '2003-05-16', 'student', 'University of the East', 'C.M. Recto Ave., Sampaloc, Manila',
                'Brgy. Sta. Rita, Olongapo City, Zambales', 'Gerardo Pascual', 'Father', '201-4', 'unpaid', 'L' => 0, 'k' => 3, 'damage' => true],
            ['Princess Joy', 'Manalo', 'female', '2002-08-08', 'working_student', 'Pamantasan ng Lungsod ng Maynila', 'General Luna St., Intramuros, Manila',
                'Brgy. Parian, Calamba City, Laguna', 'Cristina Manalo', 'Mother', '201-5', 'paid', 'L' => 7, 'k' => 3, 'unbilled_penalty' => true,
                'review' => [3, 'Okay naman overall. Sana lang may kitchen na pwedeng gamitin kasi bawal magluto sa room.']],
            ['Kathleen Mae', 'Salazar', 'female', '2006-01-30', 'student', 'University of Santo Tomas', 'España Blvd., Sampaloc, Manila',
                'Brgy. Poblacion, Tagum City, Davao del Norte', 'Rogelio Salazar', 'Father', '201-6', 'pending_movein'],

            // ---- Room 202 (Double) ----
            ['Carlo Miguel', 'Bautista', 'male', '2000-03-03', 'full_time_employee', 'Globe Telecom', 'The Globe Tower, BGC, Taguig City',
                'San Pablo City, Laguna', 'Nenita Bautista', 'Mother', '202-1', 'paid', 'L' => 9, 'k' => 2],

            // ---- Room 203 (Quad) ----
            ['Joshua Emmanuel', 'Lim', 'male', '2004-11-11', 'student', 'De La Salle University', 'Taft Ave., Malate, Manila',
                'Sta. Cruz, Laguna', 'Wilson Lim', 'Father', '203-1', 'paid', 'L' => 18, 'k' => 4,
                'review' => [4, 'Good value for money. Malinis ang CR at laging may tubig. Medyo strict sa visitors pero understandable.']],
            ['Paolo Andres', 'Ocampo', 'male', '2005-04-22', 'student', 'Adamson University', 'San Marcelino St., Ermita, Manila',
                'Brgy. Poblacion, Malolos City, Bulacan', 'Amelia Ocampo', 'Mother', '203-2', 'pending_movein', 'movein_proof' => true],

            // ---- Room 204 (Solo) ----
            ['Andrea Nicole', 'Tan', 'female', '1997-09-09', 'full_time_employee', 'SM Supermalls Corporate Office', 'Mall of Asia Complex, Pasay City',
                'Brgy. Kauswagan, Cagayan de Oro City', 'Rebecca Tan', 'Mother', '204-1', 'paid', 'L' => 6, 'k' => 5],

            // ---- Room 301 (Quad) ----
            ['Erika Jane', 'Morales', 'female', '2004-07-07', 'student', 'Far Eastern University', 'Nicanor Reyes St., Sampaloc, Manila',
                'Brgy. Pantal, Dagupan City, Pangasinan', 'Ronaldo Morales', 'Father', '301-1', 'paid', 'L' => 13, 'k' => 3],
            ['Hannah Grace', 'Soriano', 'female', '2006-02-14', 'student', 'Philippine Normal University', 'Taft Ave., Ermita, Manila',
                'Brgy. Dolores, Taytay, Rizal', 'Marilou Soriano', 'Mother', '301-2', 'paid', 'L' => 4, 'k' => 1],

            // ---- Former tenants ----
            ['Gabriel Jose', 'Rivera', 'male', '2001-06-01', 'full_time_employee', 'Meralco', 'Ortigas Ave., Pasig City',
                'Brgy. San Antonio, Biñan, Laguna', 'Imelda Rivera', 'Mother', '302-1', 'paid', 'L' => 70, 'k' => 4,
                'lease' => 'moved_out',
                'review' => [5, 'Nag-move out na ako kasi lumipat ang work ko, pero sobrang saya ng stay ko dito. Salamat NEST.PH!']],
            ['Joseph Allan', 'Cruz', 'male', '2003-03-27', 'student', 'Emilio Aguinaldo College', 'Gen. Malvar St., Malate, Manila',
                'Brgy. Tabing Ilog, Marilao, Bulacan', 'Nora Cruz', 'Mother', '303-1', 'overdue', 'L' => 50, 'k' => 4, 'days' => 45,
                'lease' => 'blacklisted'],

            // ---- More current tenants (fill the dorm to a realistic live level) ----
            // Last in the list so they're processed after the former tenants of 302-1/303-1 free those beds, and so the scenario tenants' numbering (tickets, Juan's late fee) stays the same.
            ['Stephanie Claire', 'Uy', 'female', '2004-09-02', 'student', 'University of Santo Tomas', 'España Blvd., Sampaloc, Manila',
                'Brgy. Lourdes, Dagupan City, Pangasinan', 'Henry Uy', 'Father', '101-4', 'paid', 'L' => 16, 'k' => 7,
                'review' => [5, 'Ang ganda ng study hall sa baba, dito ako nagre-review lagi. Mabilis din sumagot ang admin sa tickets.']],
            ['Adrian Paul', 'Castro', 'male', '1999-12-12', 'full_time_employee', 'Teleperformance Philippines', 'Robinsons Cybergate, Mandaluyong City',
                'Brgy. Sampaloc, Tanauan City, Batangas', 'Leticia Castro', 'Mother', '102-2', 'paid', 'L' => 11, 'k' => 3],
            ['Vincent Ray', 'Magbanua', 'male', '2005-06-19', 'student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila',
                'Brgy. Poblacion, Roxas City, Capiz', 'Ramon Magbanua', 'Father', '103-4', 'paid', 'L' => 9, 'k' => 2],
            ['Luis Antonio', 'Del Rosario', 'male', '2003-02-27', 'working_student', 'University of the East', 'C.M. Recto Ave., Sampaloc, Manila',
                'Brgy. Cutcut, Angeles City, Pampanga', 'Rowena Del Rosario', 'Mother', '302-1', 'proof_pending', 'L' => 21, 'k' => 0],
            ['Jerome Anthony', 'Pineda', 'male', '2004-10-30', 'student', 'Mapúa University', 'Muralla St., Intramuros, Manila',
                'Brgy. Poblacion, Tarlac City, Tarlac', 'Grace Pineda', 'Mother', '303-1', 'paid', 'L' => 14, 'k' => 0],
            ['Kenneth Bryan', 'Sy', 'male', '2006-01-08', 'student', 'National University', 'M.F. Jhocson St., Sampaloc, Manila',
                'Brgy. Balibago, Sta. Rosa City, Laguna', 'Victor Sy', 'Father', '303-5', 'unpaid', 'L' => 2, 'k' => 0],
            ['Emmanuel Jose', 'Villareal', 'male', '2002-07-21', 'part_time_employee', '7-Eleven (Legarda branch)', 'Legarda St., Sampaloc, Manila',
                'Brgy. San Vicente, Tacloban City, Leyte', 'Nelia Villareal', 'Mother', '303-6', 'paid', 'L' => 24, 'k' => 4],
        ];
    }

    private function seedTenants(): void
    {
        $tenantRole = DB::table('roles')->where('role_name', 'tenant')->value('id');
        $password = Hash::make(self::PASSWORD);
        $n = 0;

        foreach ($this->tenantSpecs() as $spec) {
            [$first, $last, $gender, $dob, $type, $school, $schoolAddr, $home, $ecName, $ecRel, $bedKey, $scenario] = $spec;
            $n++;

            $room = $this->rooms[explode('-', $bedKey)[0]];
            $bedId = $this->beds[$bedKey];
            $lease = $spec['lease'] ?? null;
            $isOverdue = $scenario === 'overdue';

            // Contract dates.
            if ($scenario === 'pending_movein') {
                $start = $this->today->copy()->addDays(3 + $n % 3);
                $monthsHistory = 0;
            } else {
                $start = $this->today->copy()->subDays($spec['L'])->subMonthsNoOverflow($spec['k']);
            }
            $end = match ($lease) {
                'expiring_soon' => $this->today->copy()->addDays(20),
                'expired' => $this->today->copy()->subDays(3),
                'moved_out' => $this->today->copy()->subDays($spec['L'])->addMonthNoOverflow()->subDay(),
                default => $start->copy()->addMonthsNoOverflow(max(6, ($spec['k'] ?? 0) + 6))->subDay(),
            };

            $email = $this->emailFor($first, $last);
            $phone = $spec['sms'] ?? ($isOverdue ? self::DEMO_SMS_NUMBER : $this->phone($n));
            $ecPhone = $spec['sms'] ?? ($isOverdue ? self::DEMO_SMS_NUMBER : $this->phone($n + 100));
            $appliedAt = $start->copy()->subDays(10 + $n % 5)->setTime(9 + $n % 8, 15);

            // Login account.
            $userActive = ! in_array($lease, ['moved_out'], true);
            $userId = DB::table('users')->insertGetId([
                'name' => "{$first} {$last}",
                'email' => $email,
                'password' => $password,
                'role_id' => $tenantRole,
                'is_active' => $userActive,
                'created_at' => $appliedAt->copy()->addDays(2),
                'updated_at' => $appliedAt->copy()->addDays(2),
            ]);

            // Tenant record.
            $tenantStatus = match (true) {
                $scenario === 'pending_movein' => 'pending_move_in_payment',
                $lease === 'moved_out' => 'inactive',
                default => 'active',
            };
            $tenantId = DB::table('tenants')->insertGetId([
                'user_id' => $userId,
                'first_name' => $first,
                'last_name' => $last,
                'contact_number' => $phone,
                'email' => $email,
                'emergency_contact_name' => $ecName,
                'emergency_contact_number' => $ecPhone,
                'date_of_birth' => $dob,
                'home_address' => $home,
                'tenant_type' => $type,
                'id_document_path' => $this->files['id'][$n % max(count($this->files['id']), 1)] ?? null,
                'signed_contract_path' => $this->files['contract'],
                'status' => $tenantStatus,
                'deactivation_reason' => $lease === 'moved_out' ? 'Moved out at the end of contract -- transferred to a job in Laguna.' : null,
                'deactivated_at' => $lease === 'moved_out' ? $end->copy()->addDay() : null,
                'deactivated_by' => $lease === 'moved_out' ? $this->admins['kristine'] : null,
                'is_blacklisted' => $lease === 'blacklisted',
                'portal_restricted' => $isOverdue && $spec['days'] >= 8,
                'escalation_paused' => ! empty($spec['paused']),
                'created_at' => $appliedAt->copy()->addDays(2),
                'updated_at' => now(),
            ]);

            // Application (approved) that produced this tenant.
            $applicationId = DB::table('applications')->insertGetId([
                'tenant_id' => $tenantId,
                'first_name' => $first,
                'last_name' => $last,
                'birthdate' => $dob,
                'gender' => $gender,
                'nationality' => 'Filipino',
                'medical_condition' => $n % 6 === 0 ? 'Asthma (mild, with inhaler)' : 'None',
                'occupation' => str_contains($type, 'student') ? 'Student' : 'Employee',
                'school_company' => $school,
                'school_company_address' => $schoolAddr,
                'contact_number' => $phone,
                'email' => $email,
                'home_address' => $home,
                'emergency_contact_name' => $ecName,
                'emergency_contact_number' => $ecPhone,
                'emergency_contact_email' => $this->emailFor(explode(' ', $ecName)[0], $last, true),
                'emergency_contact_relation' => $ecRel,
                'bed_id' => $bedId,
                'preferred_start_date' => $start->toDateString(),
                'tenant_end_date' => $end->toDateString(),
                'type_of_tenant' => $type,
                'id_document_path' => $this->files['id'][$n % max(count($this->files['id']), 1)] ?? null,
                'signed_contract_path' => $this->files['contract'],
                'dpa_consent' => true,
                'status' => 'approved',
                'approved_by' => $this->admins['kristine'],
                'created_at' => $appliedAt,
                'updated_at' => $appliedAt->copy()->addDays(2),
            ]);

            // Lease contract.
            $discount = $spec['discount'] ?? 0;
            $perBed = round($room->rate / $room->beds, 2);
            $rate = $perBed - $discount;
            $contractStatus = match ($lease) {
                'expiring_soon' => 'expiring_soon',
                'expired', 'moved_out' => 'expired',
                'blacklisted' => 'terminated',
                default => 'active',
            };
            $contractId = DB::table('lease_contracts')->insertGetId([
                'application_id' => $applicationId,
                'tenant_id' => $tenantId,
                'bed_id' => $bedId,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'monthly_rate' => $rate,
                'discount_amount' => $discount ?: null,
                'esign_status' => 'signed',
                'signed_document_url' => $this->files['contract'],
                'signed_at' => $appliedAt->copy()->addDays(1),
                'status' => $contractStatus,
                'termination_reason' => $lease === 'blacklisted' ? 'Terminated for non-payment after full delinquency escalation (Stage 6).' : null,
                'terminated_at' => $lease === 'blacklisted' ? $this->today->copy()->subDays(30) : null,
                'created_by' => $this->admins['kristine'],
                'approved_by' => $this->admins['kristine'],
                'created_at' => $appliedAt->copy()->addDays(2),
                'updated_at' => now(),
            ]);

            // Bed status.
            $bedStatus = match (true) {
                $scenario === 'pending_movein' => 'reserved',
                in_array($lease, ['moved_out', 'blacklisted'], true) => 'vacant',
                default => 'occupied',
            };
            DB::table('beds')->where('id', $bedId)->update(['status' => $bedStatus]);

            // Move-in fee statement (1 month deposit + 1 month advance).
            $moveIn = $rate * 2;
            $moveInBillId = DB::table('billing_statements')->insertGetId([
                'contract_id' => $contractId,
                'tenant_id' => $tenantId,
                'type' => 'move_in',
                'billing_period_start' => $start->toDateString(),
                'billing_period_end' => $start->toDateString(),
                'due_date' => $start->toDateString(),
                'base_rent' => $moveIn,
                'total_amount' => $moveIn,
                'status' => $scenario === 'pending_movein' ? 'unpaid' : 'paid',
                'created_at' => $appliedAt->copy()->addDays(2),
                'updated_at' => now(),
            ]);

            if ($scenario === 'pending_movein') {
                if (! empty($spec['movein_proof'])) {
                    $this->payment($moveInBillId, $tenantId, $moveIn, 'gcash', $this->today->copy()->subDay(), 'pending',
                        'Move-in fee (deposit + advance). Sent via GCash po.');
                }
                continue;
            }

            $this->payment($moveInBillId, $tenantId, $moveIn, 'cash', $start->copy()->subDays(1), 'approved', 'Move-in fee paid at the admin office.');

            // Monthly statements, chained exactly like BillingController::generateForContract().
            [$utilShare, $wifiShare] = [round($room->util / $room->beds, 2), round($room->wifi / $room->beds, 2)];
            $periodStart = $start->copy();
            $total = $spec['k'] + 1;

            for ($i = 0; $i < $total; $i++) {
                $periodEnd = $periodStart->copy()->addMonthNoOverflow()->subDay();
                $due = $periodStart->copy()->addDays(5);
                $isLast = $i === $total - 1;
                $base = $rate + $utilShare + $wifiShare;

                $state = $isLast ? $scenario : 'paid';
                $status = match ($state) {
                    'paid' => 'paid',
                    'partial' => 'partial',
                    'overdue' => 'overdue',
                    default => 'unpaid',
                };

                $billId = DB::table('billing_statements')->insertGetId([
                    'contract_id' => $contractId,
                    'tenant_id' => $tenantId,
                    'type' => 'monthly',
                    'billing_period_start' => $periodStart->toDateString(),
                    'billing_period_end' => $periodEnd->toDateString(),
                    'due_date' => $due->toDateString(),
                    'base_rent' => $rate,
                    'utilities_amount' => $utilShare,
                    'wifi_amount' => $wifiShare,
                    'penalty_amount' => 0,
                    'total_amount' => $base,
                    'status' => $status,
                    'created_at' => $periodStart->copy()->setTime(0, 5),
                    'updated_at' => now(),
                ]);

                // One older bill for Juan was paid late -- shows a paid late fee in history.
                if (! $isLast && $n === 5 && $i === 2) {
                    $this->penalty($tenantId, $billId, 'manual', 'Late payment fee (10% of monthly rent)', round($rate * 0.10, 2),
                        $due->copy()->addDays(4), 'active');
                    $base = $this->refreshBillTotal($billId);
                    $this->payment($billId, $tenantId, $base, 'gcash', $due->copy()->addDays(6), 'approved', 'Sorry po late, na-delay sweldo.');
                } elseif ($state === 'paid') {
                    $method = ['cash', 'gcash', 'bank_transfer'][($n + $i) % 3];
                    $this->payment($billId, $tenantId, $base, $method, $due->copy()->subDays(($n + $i) % 5), 'approved');
                }

                if ($isLast) {
                    $this->applyScenario($state, $spec, $n, $tenantId, $billId, $bedId, $room, $base, $due);
                }

                $periodStart = $periodEnd->copy()->addDay();
            }

            $this->seedTicketsFor($n, $tenantId, $bedId);

            if (isset($spec['review'])) {
                $this->review($tenantId, ...$spec['review']);
            }
        }

        // Moderation examples: one auto-hidden (flagged) review and one removed by an admin.
        $jasmine = Tenant::where('email', $this->emailFor('Jasmine Rose', 'Garcia'))->value('id');
        $carlo = Tenant::where('email', $this->emailFor('Carlo Miguel', 'Bautista'))->value('id');
        DB::table('reviews')->insert([
            ['tenant_id' => $jasmine, 'rating' => 1, 'comment' => 'Mas mura sa amin! Visit www.cheapdorms-manila.com for promo, message 0917-000-0000',
                'is_approved' => false, 'status' => 'hidden',
                'flag_reasons' => json_encode(['Contains a link', 'Contains a phone number']),
                'moderated_by' => null, 'moderated_at' => null, 'moderation_note' => null,
                'created_at' => $this->today->copy()->subDays(3), 'updated_at' => $this->today->copy()->subDays(3)],
            ['tenant_id' => $carlo, 'rating' => 2, 'comment' => 'Pangit ugali ng roommate ko, [name removed] sobrang ingay.',
                'is_approved' => false, 'status' => 'removed', 'flag_reasons' => null,
                'moderated_by' => $this->admins['kristine'], 'moderated_at' => $this->today->copy()->subDays(6),
                'moderation_note' => 'Removed: names another tenant. Concern was redirected to a support ticket instead.',
                'created_at' => $this->today->copy()->subDays(7), 'updated_at' => $this->today->copy()->subDays(6)],
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  4b. Former tenants -- 13 months of move-ins and move-outs          */
    /* ------------------------------------------------------------------ */

    /**
     * Fills the past year with tenants who already moved out, so the
     * Occupancy Trend chart and the Financial report have real history
     * to show. Each bed gets back-to-back stays (3-8 months each, with a
     * gap between tenants) that all end BEFORE that bed's current tenant
     * moved in, so no two people ever share a bed at the same time.
     *
     * Each former tenant gets the same records a real move-out leaves
     * behind: inactive account, terminated contract, fully paid bills
     * and payments, and a deposit refund.
     */
    private function seedFormerTenants(): void
    {
        mt_srand(20260930); // same "random" history every time the seeder runs

        $firstNames = ['Aldrin', 'Bianca', 'Cedric', 'Danica', 'Elijah', 'Francine', 'Gian', 'Hazel', 'Ivan', 'Janelle',
            'Kevin', 'Lianne', 'Marco', 'Nina', 'Oliver', 'Pia', 'Queenie', 'Renz', 'Sofia', 'Troy', 'Ubert', 'Vanessa',
            'Warren', 'Ysabel', 'Zach', 'Alyssa', 'Bryan', 'Clarisse', 'Dominic', 'Ella', 'Franco', 'Gwen', 'Harold', 'Irish',
            'Jericho', 'Kyla', 'Lance', 'Mika', 'Nathan', 'Rica'];
        $lastNames = ['Abad', 'Buenaventura', 'Castillo', 'Dizon', 'Enriquez', 'Fernandez', 'Galang', 'Hernandez', 'Ilagan',
            'Javier', 'Lacson', 'Macaraeg', 'Nepomuceno', 'Ortega', 'Panganiban', 'Quiambao', 'Rosales', 'Sison', 'Tolentino',
            'Umali', 'Valdez', 'Yap', 'Zamora', 'Agustin', 'Belmonte', 'Cordero', 'Domingo', 'Estrada', 'Figueroa', 'Guevarra'];
        $reasons = [
            'Graduated -- moved back home to the province.',
            'Moved out at the end of contract.',
            'Transferred to a dorm closer to the new campus.',
            'Found a job in another city.',
            'Moved in with relatives in Quezon City.',
            'Semester ended; will not be renewing.',
        ];
        $types = [['student', 'Student'], ['student', 'Student'], ['working_student', 'Student'], ['full_time_employee', 'Employee']];
        $schools = ['University of Santo Tomas', 'Far Eastern University', 'Polytechnic University of the Philippines',
            'University of the East', 'Centro Escolar University', 'National University', 'Accenture Philippines', 'Concentrix Manila'];

        $tenantRole = DB::table('roles')->where('role_name', 'tenant')->value('id');
        $password = Hash::make(self::PASSWORD);
        $usedEmails = DB::table('users')->pluck('email')->flip()->all();
        $historyStart = $this->today->copy()->subMonthsNoOverflow(13)->startOfMonth();
        $n = 200; // keeps phone numbers / picks distinct from the scenario tenants
        $made = 0;

        foreach ($this->beds as $bedKey => $bedId) {
            [$roomNo] = explode('-', $bedKey);
            if ($roomNo === '104' || $bedKey === '203-4') {
                continue; // under maintenance
            }
            $room = $this->rooms[$roomNo];

            // Former stays must end a week before the bed's current/next tenant starts.
            $nextStart = DB::table('lease_contracts')->where('bed_id', $bedId)->min('start_date');
            $limit = $nextStart
                ? Carbon::parse($nextStart)->subDays(7)
                : $this->today->copy()->subDays(mt_rand(15, 240)); // empty now: last tenant left sometime this year

            $cursor = $historyStart->copy()->addDays(mt_rand(0, 45));
            while (true) {
                $moveOut = $cursor->copy()->addMonthsNoOverflow(mt_rand(3, 8));
                if ($moveOut->gt($limit)) {
                    // Shorten the last stay to fit, as long as it's still 2+ months.
                    if ($cursor->copy()->addMonthsNoOverflow(2)->gt($limit)) {
                        break;
                    }
                    $moveOut = $limit->copy();
                }

                // Unique first + last name pair.
                do {
                    $first = $firstNames[mt_rand(0, count($firstNames) - 1)];
                    $last = $lastNames[mt_rand(0, count($lastNames) - 1)];
                    $email = $this->emailFor($first, $last);
                } while (isset($usedEmails[$email]));
                $usedEmails[$email] = true;

                $n++;
                $made++;
                [$type, $occupation] = $types[$n % count($types)];
                $this->formerTenant($first, $last, $email, $type, $occupation, $schools[$n % count($schools)],
                    $reasons[$n % count($reasons)], $bedId, $room, $cursor, $moveOut, $n, $tenantRole, $password);

                $cursor = $moveOut->copy()->addDays(mt_rand(5, 55));
                if ($cursor->gte($limit)) {
                    break;
                }
            }
        }

        $this->command?->info("Seeded {$made} former (moved-out) tenants.");
    }

    private function formerTenant(string $first, string $last, string $email, string $type, string $occupation, string $school,
        string $reason, int $bedId, object $room, Carbon $start, Carbon $moveOut, int $n, int $tenantRole, string $password): void
    {
        $appliedAt = $start->copy()->subDays(10 + $n % 7)->setTime(9 + $n % 8, 30);
        $phone = $this->phone($n);
        $home = ['Brgy. Poblacion, Batangas City', 'Brgy. San Jose, Tarlac City', 'Brgy. Centro, Naga City',
            'Brgy. Malabanias, Angeles City', 'Brgy. Bagumbayan, Lucena City'][$n % 5];
        $parent = ['Mother', 'Father'][$n % 2];
        $parentName = (['Teresa', 'Roberto', 'Lourdes', 'Ricardo', 'Elena', 'Manuel'][$n % 6]) . ' ' . $last;

        $userId = DB::table('users')->insertGetId([
            'name' => "{$first} {$last}",
            'email' => $email,
            'password' => $password,
            'role_id' => $tenantRole,
            'is_active' => false,
            'created_at' => $appliedAt->copy()->addDays(2),
            'updated_at' => $moveOut,
        ]);

        $tenantId = DB::table('tenants')->insertGetId([
            'user_id' => $userId,
            'first_name' => $first,
            'last_name' => $last,
            'contact_number' => $phone,
            'email' => $email,
            'emergency_contact_name' => $parentName,
            'emergency_contact_number' => $this->phone($n + 100),
            'date_of_birth' => $this->today->copy()->subYears($type === 'full_time_employee' ? 25 : 20)->subDays($n * 13 % 300)->toDateString(),
            'home_address' => $home,
            'tenant_type' => $type,
            'id_document_path' => $this->files['id'][$n % max(count($this->files['id']), 1)] ?? null,
            'signed_contract_path' => $this->files['contract'],
            'status' => 'inactive',
            'deactivation_reason' => $reason,
            'deactivated_at' => $moveOut->copy()->setTime(10, 0),
            'deactivated_by' => $this->admins['kristine'],
            'is_blacklisted' => false,
            'portal_restricted' => false,
            'escalation_paused' => false,
            'created_at' => $appliedAt->copy()->addDays(2),
            'updated_at' => $moveOut,
        ]);

        $applicationId = DB::table('applications')->insertGetId([
            'tenant_id' => $tenantId,
            'first_name' => $first,
            'last_name' => $last,
            'birthdate' => $this->today->copy()->subYears(20)->toDateString(),
            'gender' => in_array($first, ['Bianca', 'Danica', 'Francine', 'Hazel', 'Janelle', 'Lianne', 'Nina', 'Pia', 'Queenie', 'Sofia',
                'Vanessa', 'Ysabel', 'Alyssa', 'Clarisse', 'Ella', 'Gwen', 'Irish', 'Kyla', 'Mika', 'Rica'], true) ? 'female' : 'male',
            'nationality' => 'Filipino',
            'medical_condition' => 'None',
            'occupation' => $occupation,
            'school_company' => $school,
            'school_company_address' => 'Manila',
            'contact_number' => $phone,
            'email' => $email,
            'home_address' => $home,
            'emergency_contact_name' => $parentName,
            'emergency_contact_number' => $this->phone($n + 100),
            'emergency_contact_email' => $this->emailFor(explode(' ', $parentName)[0], $last, true),
            'emergency_contact_relation' => $parent,
            'bed_id' => $bedId,
            'preferred_start_date' => $start->toDateString(),
            'tenant_end_date' => $moveOut->toDateString(),
            'type_of_tenant' => $type,
            'id_document_path' => $this->files['id'][$n % max(count($this->files['id']), 1)] ?? null,
            'signed_contract_path' => $this->files['contract'],
            'dpa_consent' => true,
            'status' => 'approved',
            'approved_by' => $this->admins['kristine'],
            'created_at' => $appliedAt,
            'updated_at' => $appliedAt->copy()->addDays(2),
        ]);

        $rate = round($room->rate / $room->beds, 2);
        $contractId = DB::table('lease_contracts')->insertGetId([
            'application_id' => $applicationId,
            'tenant_id' => $tenantId,
            'bed_id' => $bedId,
            'start_date' => $start->toDateString(),
            'end_date' => $moveOut->toDateString(),
            'monthly_rate' => $rate,
            'discount_amount' => null,
            'esign_status' => 'signed',
            'signed_document_url' => $this->files['contract'],
            'signed_at' => $appliedAt->copy()->addDays(1),
            // Same result as the real "Deactivate tenant" flow (TenantController::setStatus).
            'status' => 'terminated',
            'termination_reason' => $reason,
            'terminated_at' => $moveOut->copy()->setTime(10, 0),
            'created_by' => $this->admins['kristine'],
            'approved_by' => $this->admins['kristine'],
            'created_at' => $appliedAt->copy()->addDays(2),
            'updated_at' => $moveOut,
        ]);

        // Move-in fee (deposit + advance), paid.
        $moveIn = $rate * 2;
        $moveInBillId = DB::table('billing_statements')->insertGetId([
            'contract_id' => $contractId,
            'tenant_id' => $tenantId,
            'type' => 'move_in',
            'billing_period_start' => $start->toDateString(),
            'billing_period_end' => $start->toDateString(),
            'due_date' => $start->toDateString(),
            'base_rent' => $moveIn,
            'total_amount' => $moveIn,
            'status' => 'paid',
            'created_at' => $appliedAt->copy()->addDays(2),
            'updated_at' => $start,
        ]);
        $this->payment($moveInBillId, $tenantId, $moveIn, 'cash', $start->copy()->subDay(), 'approved', 'Move-in fee paid at the admin office.');

        // Monthly bills for every month they stayed, all paid.
        [$utilShare, $wifiShare] = [round($room->util / $room->beds, 2), round($room->wifi / $room->beds, 2)];
        $total = $rate + $utilShare + $wifiShare;
        $periodStart = $start->copy();
        $i = 0;
        while ($periodStart->lt($moveOut)) {
            $periodEnd = $periodStart->copy()->addMonthNoOverflow()->subDay();
            $due = $periodStart->copy()->addDays(5);
            $billId = DB::table('billing_statements')->insertGetId([
                'contract_id' => $contractId,
                'tenant_id' => $tenantId,
                'type' => 'monthly',
                'billing_period_start' => $periodStart->toDateString(),
                'billing_period_end' => $periodEnd->toDateString(),
                'due_date' => $due->toDateString(),
                'base_rent' => $rate,
                'utilities_amount' => $utilShare,
                'wifi_amount' => $wifiShare,
                'penalty_amount' => 0,
                'total_amount' => $total,
                'status' => 'paid',
                'created_at' => $periodStart->copy()->setTime(0, 5),
                'updated_at' => $due,
            ]);
            $method = ['cash', 'gcash', 'bank_transfer', 'gcash'][($n + $i) % 4];
            $this->payment($billId, $tenantId, $total, $method, $due->copy()->subDays(($n + $i) % 5), 'approved');
            $periodStart = $periodEnd->copy()->addDay();
            $i++;
        }

        // Deposit returned after move-out (sometimes minus a small deduction).
        $deduction = $n % 4 === 0 ? 500.0 : 0.0;
        DB::table('deposit_refunds')->insert([
            'tenant_id' => $tenantId,
            'deposit_amount' => $rate,
            'deductions_amount' => $deduction,
            'deductions_note' => $deduction ? 'Replacement of lost room key.' : null,
            'refund_amount' => $rate - $deduction,
            'refund_method' => $n % 2 ? 'GCash' : 'Cash',
            'reference_number' => $n % 2 ? $this->refNo('gcash') : null,
            'refunded_at' => $moveOut->copy()->addDays(3)->toDateString(),
            'recorded_by' => $this->admins['kristine'],
            'created_at' => $moveOut->copy()->addDays(3),
            'updated_at' => $moveOut->copy()->addDays(3),
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  4c. Monthly expenses -- the dorm's own bills                       */
    /* ------------------------------------------------------------------ */

    /**
     * 12 months of the building's own costs, so Reports can show Net
     * Profit (payments collected minus these). Meralco goes up in the hot
     * months (March-May), December has 13th-month pay, and a few months
     * have a one-off "others" cost.
     */
    private function seedMonthlyExpenses(): void
    {
        mt_srand(4242);
        $oneOffs = [
            2 => [8500, 'Aircon cleaning (all rooms)'],
            5 => [3200, 'Plumbing repair, 2F shared CR'],
            8 => [12000, 'Repainting of hallway and lobby'],
            10 => [2500, 'Pest control'],
            11 => [4800, 'Replacement of water pump capacitor'],
        ];

        for ($i = 12; $i >= 1; $i--) { // the last 12 finished months; this month is left for the admin to enter
            $month = $this->today->copy()->startOfMonth()->subMonthsNoOverflow($i);
            $hot = in_array($month->month, [3, 4, 5], true);
            $salaries = 27000 + ($month->month === 12 ? 27000 : 0); // caretaker 15k + cleaner 12k, plus 13th month
            [$other, $note] = $oneOffs[$i] ?? [mt_rand(0, 1) ? mt_rand(800, 2000) : 0, 'Cleaning supplies and toiletries for common areas'];

            DB::table('monthly_expenses')->insert([
                'month' => $month->toDateString(),
                'electricity' => round(($hot ? 58000 : 44000) + mt_rand(-3000, 4000), 2),
                'water' => round(4600 + mt_rand(-600, 1200), 2),
                'internet' => 3499,
                'salaries' => $salaries,
                'other' => $other,
                'other_notes' => $other ? $note : null,
                'recorded_by' => $this->admins['mark'],
                'created_at' => $month->copy()->endOfMonth(),
                'updated_at' => $month->copy()->endOfMonth(),
            ]);
        }
    }

    /** What happens to each scenario's latest bill. */
    private function applyScenario(string $state, array $spec, int $n, int $tenantId, int $billId, int $bedId, object $room, float $amount, Carbon $due): void
    {
        switch ($state) {
            case 'partial':
                $this->payment($billId, $tenantId, round($amount / 2, -2), 'cash', $this->today->copy()->subDay(), 'approved',
                    'Partial muna po, babayaran ko yung natitira sa sweldo.');
                break;

            case 'proof_pending':
                $this->payment($billId, $tenantId, $amount, 'gcash', $this->today->copy(), 'pending', 'Time of payment: 08:42. Full payment for this month po.');
                break;

            case 'proof_rejected':
                $this->payment($billId, $tenantId, $amount, 'gcash', $this->today->copy()->subDay(), 'rejected', 'Bayad ko po for this month.',
                    'Screenshot is cropped -- the reference number and amount are not visible. Please upload the full receipt.');
                break;

            case 'unpaid':
                if (! empty($spec['damage'])) {
                    $this->damageWithPenalty($tenantId, $billId, $bedId, $room);
                }
                break;

            case 'paid':
                if (! empty($spec['unbilled_penalty'])) {
                    // Not on any bill yet -- demo "attach penalties" / next generated bill picking it up.
                    $this->penalty($tenantId, null, 'manual', 'House rule violation: butane stove found in room (hazardous goods)', 500,
                        $this->today->copy()->subDays(2), 'active');
                }
                break;

            case 'overdue':
                $days = $spec['days'];
                // Late fee (10% of rent) once past the 3-day grace period.
                if ($days > 3) {
                    $rent = (float) DB::table('billing_statements')->where('id', $billId)->value('base_rent');
                    $this->penalty($tenantId, $billId, 'manual', 'Late payment fee (10% of monthly rent)', round($rent * 0.10, 2),
                        $due->copy()->addDays(4), 'active');
                    $this->refreshBillTotal($billId);
                }
                $this->escalationHistory($tenantId, $billId, $due, $days, ! empty($spec['paused']));
                break;
        }
    }

    /**
     * Writes the escalation log rows the engine WOULD have written by now,
     * so running `escalation:process` only does the next step instead of
     * replaying every SMS at once.
     */
    private function escalationHistory(int $tenantId, int $billId, Carbon $due, int $days, bool $paused): void
    {
        $bill = DB::table('billing_statements')->where('id', $billId)->first();
        $tenant = DB::table('tenants')->where('id', $tenantId)->first();
        $balance = number_format((float) $bill->total_amount, 2);
        $name = "{$tenant->first_name} {$tenant->last_name}";

        $steps = [
            [0, 1, 'account_flagged', null, 'resolved'],
        ];
        foreach ([2, 4, 7] as $d) {
            $urgency = $d >= 7 ? 'URGENT' : 'Reminder';
            $steps[] = [$d, 2, "sms_reminder_day{$d}",
                "{$urgency}: Your account with NEST.PH is now {$d} day(s) overdue. Outstanding balance (incl. penalties): PHP {$balance}. Please pay via the tenant portal to avoid further account restrictions.",
                'sent'];
        }
        $steps[] = [8, 3, 'portal_restricted', 'Your account access has been restricted due to unpaid balance. Please settle your balance to restore full access. - NEST.PH', 'sent'];
        $steps[] = [9, 4, 'emergency_contact_notified',
            "This is to inform you that {$name}'s account at NEST.PH is 9 days overdue, balance PHP {$balance}. Please encourage them to settle it as soon as possible.",
            'sent'];
        $steps[] = [10, 5, 'demand_letter_generated', null, 'sent'];
        $steps[] = [11, 6, 'delinquent_blacklisted', null, 'resolved'];

        foreach ($steps as [$day, $stage, $action, $message, $status]) {
            if ($days < $day) {
                break;
            }
            if ($action === 'demand_letter_generated') {
                $message = $this->demandLetter($tenantId, $billId);
            }
            $at = $due->copy()->addDays($day)->setTime(6, 0);
            DB::table('escalation_logs')->insert([
                'tenant_id' => $tenantId,
                'billing_id' => $billId,
                'stage' => $stage,
                'action_type' => $action,
                'message_content' => $message,
                'status' => $status,
                'performed_by' => null,
                'created_at' => $at,
                'updated_at' => $at,
            ]);
        }

        if ($paused) {
            DB::table('escalation_logs')->insert([
                'tenant_id' => $tenantId,
                'billing_id' => null,
                'stage' => 2,
                'action_type' => 'admin_override_pause',
                'message_content' => 'Paused by admin: tenant agreed to a payment plan (half on the 15th, half on the 30th).',
                'status' => 'resolved',
                'performed_by' => $this->admins['kristine'],
                'created_at' => $this->today->copy()->subDay()->setTime(14, 20),
                'updated_at' => $this->today->copy()->subDay()->setTime(14, 20),
            ]);
        }
    }

    /** Generates a real demand letter PDF the same way EscalationService does. */
    private function demandLetter(int $tenantId, int $billId): ?string
    {
        try {
            $tenant = Tenant::find($tenantId);
            $bills = \App\Models\BillingStatement::where('tenant_id', $tenantId)->where('status', 'overdue')->orderBy('billing_period_start')->get();
            $deadline = now()->addDays(7);

            $pdf = Pdf::loadView('pdfs.demand-letter', [
                'tenant' => $tenant,
                'bills' => $bills,
                'totalOwed' => (float) $bills->sum('total_amount'),
                'totalPenalties' => (float) $bills->sum('penalty_amount'),
                'history' => $tenant->escalationLogs()->orderBy('created_at')->get(),
                'deadline' => $deadline->format('F j, Y'),
                'blacklistDate' => $deadline->copy()->addDay()->format('F j, Y'),
                'dormName' => \App\Models\DormitoryProfile::current()->dorm_name ?? 'NEST.PH',
            ]);

            $path = "demand-letters/{$tenantId}_{$billId}.pdf";
            Storage::disk('public')->put($path, $pdf->output());

            return $path;
        } catch (\Throwable $e) {
            $this->command?->warn('Demand letter PDF not generated: ' . $e->getMessage());

            return null;
        }
    }

    private function damageWithPenalty(int $tenantId, int $billId, int $bedId, object $room): void
    {
        $date = $this->today->copy()->subDays(12);
        $damageId = DB::table('damages')->insertGetId([
            'tenant_id' => $tenantId,
            'room_id' => $room->id,
            'bed_id' => $bedId,
            'description' => 'Broken cabinet door hinge (bed-side locker)',
            'cost' => 800,
            'date_incurred' => $date->toDateString(),
            'created_by' => $this->admins['jerome'],
            'created_at' => $date,
            'updated_at' => $date,
        ]);
        $this->penalty($tenantId, $billId, 'damage', 'Damage: Broken cabinet door hinge (bed-side locker)', 800, $date, 'active', $damageId);
        $this->refreshBillTotal($billId);

        // A waived penalty with its audit trail.
        $waivedId = $this->penalty($tenantId, null, 'manual', 'Lost room key replacement', 50, $this->today->copy()->subDays(40), 'waived');
        DB::table('penalty_audit_logs')->insert([
            'penalty_id' => $waivedId,
            'action' => 'waived',
            'performed_by' => $this->ownerId,
            'reason' => 'Key was found by the guard the next day. First offense -- waived.',
            'created_at' => $this->today->copy()->subDays(39),
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  5. Maintenance tickets & reviews                                   */
    /* ------------------------------------------------------------------ */

    private function seedTicketsFor(int $n, int $tenantId, int $bedId): void
    {
        // tenant # => [title, category, description, priority, status, daysAgo, assignee, replies[]]
        $tickets = [
            1 => ['Aircon not cooling', 'maintenance_repairs', 'The aircon in Room 101 blows air but it is not cold anymore since last night.', 'non_urgent', 'resolved', 20, 'jerome',
                [['admin', 'jerome', 'Noted po. Papupuntahin namin ang technician bukas ng umaga.'], ['tenant', null, 'Salamat po!'], ['admin', 'jerome', 'Nalinis na po ang filter at na-recharge ang freon. Paki-check po kung okay na.']]],
            2 => ['Sparking outlet near Bed 2', 'electrical_issue', 'The outlet beside my bed sparked when I plugged in my charger. I stopped using it.', 'urgent', 'open', 0, null, []],
            5 => ['Low water pressure in CR', 'plumbing_water_emergency', 'Mahina po ang tulo ng tubig sa shower tuwing 6-7 AM.', 'non_urgent', 'in_progress', 4, 'jerome',
                [['admin', 'jerome', 'Chine-check na po ng plumber ang main line. Update po kami mamaya.']]],
            8 => ['Noisy neighbors after curfew', 'noise_roommate_concern', 'May maingay po sa kabilang room (104?) past 12 midnight, 3 nights na.', 'non_urgent', 'seen', 2, null, []],
            11 => ['Request for a bigger study table in the lobby', 'suggestion_feedback', 'Suggestion lang po: sana may mas malaking study table sa lobby for group study.', 'non_urgent', 'rejected', 15, 'kristine',
                [['admin', 'kristine', 'Thank you for the suggestion! Hindi po kasya sa space ng lobby sa ngayon, pero isasama namin sa renovation plan next year.']]],
            12 => ['Question about my rejected GCash payment', 'billing_payment_concern', 'Bakit po na-reject yung payment ko? Nagbayad naman po ako.', 'non_urgent', 'in_progress', 0, 'mark',
                [['admin', 'mark', 'Hi Jasmine, naka-crop po kasi yung screenshot kaya hindi makita ang reference number. Paki-upload po ulit yung buong receipt.']]],
            15 => ['Broken door lock', 'security_concern', 'Hindi po nagla-lock nang maayos ang pinto ng Room 201, kailangan pang itulak.', 'urgent', 'resolved', 9, 'jerome',
                [['admin', 'jerome', 'Napalitan na po ang lock. Paki-kuha po ang bagong susi sa front desk.']]],
            18 => ['WiFi keeps disconnecting', 'facilities_amenities', 'The WiFi on the 2nd floor drops every 10-15 minutes, hard to attend online classes.', 'non_urgent', 'open', 1, null, []],
            20 => ['Ceiling leak in solo room', 'structural_damage', 'May tumutulo po sa kisame tuwing malakas ang ulan, malapit sa bintana.', 'urgent', 'in_progress', 3, 'jerome',
                [['admin', 'jerome', 'Na-inspect na po, may crack sa roof gutter. Schedule ang repair ngayong Sabado.'], ['tenant', null, 'Sige po, thank you. Ililipat ko muna yung gamit ko.']]],
        ];

        if (! isset($tickets[$n])) {
            return;
        }

        [$title, $cat, $desc, $prio, $status, $daysAgo, $assignee, $replies] = $tickets[$n];
        $created = $this->today->copy()->subDays($daysAgo)->setTime(10 + $n % 8, 30);
        if ($daysAgo === 0) {
            $created = now()->subHours(2);
        }

        $ticketId = DB::table('maintenance_tickets')->insertGetId([
            'tenant_id' => $tenantId,
            'bed_id' => $bedId,
            'title' => $title,
            'category' => $cat,
            'description' => $desc,
            'attachment_paths' => null,
            'priority' => $prio,
            'status' => $status,
            'assigned_to' => $assignee ? $this->admins[$assignee] : null,
            'resolved_at' => $status === 'resolved' ? $created->copy()->addDays(2) : null,
            'created_at' => $created,
            'updated_at' => $created->copy()->addHours(count($replies) + 1),
        ]);

        foreach ($replies as $i => [$who, $admin, $message]) {
            DB::table('ticket_replies')->insert([
                'ticket_id' => $ticketId,
                'user_id' => $who === 'admin' ? $this->admins[$admin] : null,
                'tenant_id' => $who === 'tenant' ? $tenantId : null,
                'message' => $message,
                'created_at' => min($created->copy()->addHours(($i + 1) * 3), now()),
            ]);
        }
    }

    private function review(int $tenantId, int $rating, string $comment): void
    {
        $at = $this->today->copy()->subDays(5 + $tenantId % 20);
        DB::table('reviews')->insert([
            'tenant_id' => $tenantId,
            'rating' => $rating,
            'comment' => $comment,
            'is_approved' => true,
            'status' => 'published',
            'created_at' => $at,
            'updated_at' => $at,
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  6. Applications still in the pipeline                              */
    /* ------------------------------------------------------------------ */

    private function seedPendingApplications(): void
    {
        $apps = [
            // first, last, gender, dob, type, school, bed, status, note
            // "Ana" in the walkthrough script: pending, approved live in Part 2.
            ['Ana Beatriz', 'Salonga', 'female', '2006-03-08', 'student', 'University of Santo Tomas', '202-2', 'pending', null, 2],
            ['Ryan Christopher', 'Santiago', 'male', '2005-09-18', 'student', 'University of Santo Tomas', '203-3', 'pending', null, 1],
            ['Alyssa Mae', 'Mercado', 'female', '2006-04-03', 'student', 'Far Eastern University', '301-3', 'pending', null, 0],
            ['Kevin James', 'Dizon', 'male', '2000-10-10', 'full_time_employee', 'Concentrix Philippines', '302-2', 'pending', null, 2],
            ['Sophia Isabel', 'Lopez', 'female', '2004-05-25', 'student', 'San Beda University', '303-2', 'rejected',
                'Requested stay is only 1 month; the dormitory requires a minimum 3-month contract.', 6],
            ['Daniel Lorenzo', 'Cruz', 'male', '2005-01-15', 'student', 'Mapúa University', '303-3', 're_application_requested',
                'Uploaded ID is blurry and the name cannot be read. Please re-apply with a clear photo of a valid school or government ID.', 4],
            ['Mika Ella', 'Santos', 'female', '2006-08-12', 'student', 'Centro Escolar University', '303-5', 'cancelled', null, 9],
        ];

        foreach ($apps as $i => [$first, $last, $gender, $dob, $type, $school, $bedKey, $status, $note, $daysAgo]) {
            $created = $this->today->copy()->subDays($daysAgo)->setTime(13 + $i, 5);
            $row = [
                'first_name' => $first,
                'last_name' => $last,
                'contact_number' => $first === 'Ana Beatriz' ? self::ANA_SMS_NUMBER : $this->phone(200 + $i),
                'email' => $this->emailFor($first, $last),
                'emergency_contact_name' => ['Ramon', 'Rommel', 'Liza', 'Jun', 'Maricel', 'Bong', 'Tess'][$i] . " {$last}",
                'emergency_contact_number' => $this->phone(300 + $i),
                'emergency_contact_relation' => $i % 2 ? 'Mother' : 'Father',
                'bed_id' => $this->beds[$bedKey],
                'preferred_start_date' => $this->today->copy()->addDays(3 + $i * 3)->toDateString(),
                'tenant_end_date' => $this->today->copy()->addMonths(6)->toDateString(),
            ];

            DB::table('applications')->insert($row + [
                'birthdate' => $dob,
                'gender' => $gender,
                'nationality' => 'Filipino',
                'medical_condition' => 'None',
                'occupation' => $type === 'student' ? 'Student' : 'Employee',
                'school_company' => $school,
                'school_company_address' => 'Manila',
                'home_address' => ['Brgy. Poblacion, Bontoc, Mountain Province', 'Brgy. Bagumbayan, Taguig City', 'Brgy. Malinta, Valenzuela City', 'Brgy. San Isidro, Cainta, Rizal',
                    'Brgy. Poblacion, Muntinlupa City', 'Brgy. Tambo, Parañaque City', 'Brgy. Sto. Niño, Marikina City'][$i],
                'type_of_tenant' => $type,
                'id_document_path' => $this->files['id'][$i % max(count($this->files['id']), 1)] ?? null,
                'signed_contract_path' => $this->signedApplicationContract($row, $created),
                'dpa_consent' => true,
                'status' => $status,
                'rejection_reason' => $status === 'rejected' ? $note : null,
                're_application_note' => $status === 're_application_requested' ? $note : null,
                'created_at' => $created,
                'updated_at' => $status === 'pending' ? $created : $created->copy()->addDay(),
            ]);

            if ($status === 'pending') {
                DB::table('beds')->where('id', $this->beds[$bedKey])->update(['status' => 'reserved']);
            }
        }
    }

    /* ------------------------------------------------------------------ */
    /*  7. Public inquiries                                                */
    /* ------------------------------------------------------------------ */

    private function seedInquiries(): void
    {
        $rows = [
            ['Aira Nicole Dimaculangan', 'Hello po! May available pa po bang bedspace for female this November? Magkano po ang quad sharing?', 'Quad Sharing', '301', 'new', null, 0],
            ['Rhenz Adrian Pacheco', 'Good day! Pwede po ba mag-ocular visit this Saturday? Working po ako sa Makati, looking for a solo room.', 'Solo Room', '304', 'new', null, 1],
            ['Janella Marie Quiambao', 'Kasama na po ba ang WiFi at kuryente sa monthly rate?', 'Double Sharing', null, 'contacted',
                'Hi Janella! Hiwalay po ang utilities at WiFi pero hati-hati ang room mates, around ₱900/month per bed para sa quad. Welcome po kayong mag-visit!', 3],
            ['Luis Gabriel Soriano', 'Is there a curfew? I have night classes until 9 PM.', '6-Bed Dormitory', '303', 'contacted',
                'Hello Luis! Curfew is 11 PM to 4 AM, so 9 PM classes are no problem. Feel free to apply online through our website.', 5],
            ['Ryan Christopher Santiago', 'Interested po ako sa Room 203, pwede po ba mag-apply online?', 'Quad Sharing', '203', 'converted',
                'Yes po! Na-send na namin ang link. Paki-fill up lang po ang application form.', 7],
            ['Mylene Castillo', 'Pwede po ba ang pets? May maliit po akong pusa.', null, null, 'closed',
                'Sorry po, strict NO PETS policy po kami. Salamat sa interest!', 12],
        ];

        foreach ($rows as $i => [$name, $msg, $type, $roomNo, $status, $reply, $daysAgo]) {
            $created = $this->today->copy()->subDays($daysAgo)->setTime(9 + $i * 2, 12);
            if ($daysAgo === 0) {
                $created = now()->subHours(3);
            }
            [$first, $lastName] = [explode(' ', $name)[0], last(explode(' ', $name))];
            DB::table('inquiries')->insert([
                'full_name' => $name,
                'contact_number' => $this->phone(400 + $i),
                'email' => $this->emailFor($first, $lastName),
                'room_id' => $roomNo ? $this->rooms[$roomNo]->id : null,
                'message' => $msg,
                'reply_message' => $reply,
                'replied_at' => $reply ? $created->copy()->addHours(5) : null,
                'replied_by' => $reply ? $this->admins['kristine'] : null,
                'preferred_room_type' => $type,
                'dpa_consent' => true,
                'status' => $status,
                'created_at' => $created,
                'updated_at' => $reply ? $created->copy()->addHours(5) : $created,
            ]);
        }
    }

    /* ------------------------------------------------------------------ */
    /*  8. Announcements                                                   */
    /* ------------------------------------------------------------------ */

    private function seedAnnouncements(): void
    {
        $t = fn (string $first, string $last) => DB::table('tenants')->where('email', $this->emailFor($first, $last))->value('id');

        $posts = [
            [$this->ownerId, "📢 SCHEDULED WATER INTERRUPTION\n\nMaynilad will have a water interruption this Saturday from 9:00 AM to 4:00 PM. Please store enough water the night before. Salamat po sa pag-unawa!", false, 1,
                [[null, $t('Maria Angelica', 'Santos'), 'Noted po, thank you sa heads up!'],
                 [null, $t('Mark Joseph', 'Aquino'), 'Buong araw po ba walang tubig sa lahat ng floors?'],
                 [$this->admins['kristine'], null, 'Opo, lahat ng floors po. May drum ng tubig sa ground floor na pwede gamitin.']]],
            [$this->admins['kristine'], "Reminder: Rent for this month is due on your billing due date. You may pay via Cash (admin office), GCash, or BDO. Please upload your proof of payment in the portal so we can verify it right away.", false, 6,
                [[null, $t('Christian Dave', 'Torres'), 'Pwede po ba partial muna ngayong week?'],
                 [$this->admins['mark'], null, 'Pwede po, pero may 10% late fee kung lumampas sa 3-day grace period ang natitirang balance.']]],
            [$this->ownerId, "Fire drill and building inspection next Wednesday, 3:00 PM. Attendance is required for all tenants who are in the building. Comments are turned off for this post.", true, 10, []],
        ];

        foreach ($posts as [$by, $body, $restricted, $daysAgo, $comments]) {
            $at = $this->today->copy()->subDays($daysAgo)->setTime(8, 30);
            $id = DB::table('announcements')->insertGetId([
                'user_id' => $by,
                'body' => $body,
                'comments_restricted' => $restricted,
                'created_at' => $at,
                'updated_at' => $at,
            ]);
            foreach ($comments as $i => [$userId, $tenantId, $text]) {
                DB::table('announcement_comments')->insert([
                    'announcement_id' => $id,
                    'user_id' => $userId,
                    'tenant_id' => $tenantId,
                    'body' => $text,
                    'created_at' => $at->copy()->addHours($i * 2 + 1),
                ]);
            }
        }
    }

    /** Room 202 (Double, PHP 9,500/room) = 4,750 per bed; move-in = deposit + advance. */
    private function anaMoveInFee(): float
    {
        return (9500 / 2) * 2;
    }

    /**
     * Walkthrough step 1.5: Room 102 keeps its 2 VR photos but loses the
     * arrows between them, so Vince can add one live. Room 101 keeps its
     * arrows for Ana to click in step 1.3.
     */
    private function prepareVrDemo(): void
    {
        $roomId = $this->rooms['102']->id ?? null;
        if (! $roomId) {
            return;
        }
        $scenes = DB::table('vr_scenes')->where('room_id', $roomId)->pluck('id');
        DB::table('vr_hotspots')->whereIn('vr_scene_id', $scenes)->delete();
    }

    /* ------------------------------------------------------------------ */
    /*  Helpers                                                            */
    /* ------------------------------------------------------------------ */

    private function syncRoomStatuses(): void
    {
        foreach ($this->rooms as $no => $room) {
            if ((string) $no === '104') {
                continue; // whole room under maintenance
            }
            $hasVacant = DB::table('beds')->where('room_id', $room->id)->where('status', 'vacant')->exists();
            DB::table('rooms')->where('id', $room->id)->update(['status' => $hasVacant ? 'available' : 'full']);
        }
    }

    private function payment(int $billId, int $tenantId, float $amount, string $method, Carbon $date, string $status, ?string $notes = null, ?string $review = null): void
    {
        $online = $method !== 'cash';
        $reviewed = $status !== 'pending';

        DB::table('payments')->insert([
            'billing_id' => $billId,
            'tenant_id' => $tenantId,
            'amount_paid' => $amount,
            'payment_method' => $method,
            'payment_method_label' => ['cash' => 'Cash Payment', 'gcash' => 'GCash', 'bank_transfer' => 'BDO Bank Transfer'][$method],
            'reference_number' => $online ? $this->refNo($method) : null,
            'payment_date' => $date->toDateString(),
            'status' => $status,
            'proof_path' => $online ? ($this->files['proof'] ?? null) : null,
            'notes' => $notes,
            'review_notes' => $review,
            'reviewed_by' => $online && $reviewed ? $this->admins['mark'] : null,
            'reviewed_at' => $online && $reviewed ? $date->copy()->addDay()->setTime(10, 0) : null,
            'recorded_by' => $online ? null : $this->admins['mark'],
            'created_at' => $date->copy()->setTime(9 + $billId % 9, 15),
        ]);
    }

    private function penalty(int $tenantId, ?int $billId, string $type, string $desc, float $amount, Carbon $date, string $status, ?int $damageId = null): int
    {
        $id = DB::table('penalties')->insertGetId([
            'tenant_id' => $tenantId,
            'damage_id' => $damageId,
            'billing_id' => $billId,
            'type' => $type,
            'description' => $desc,
            'amount' => $amount,
            'date_incurred' => $date->toDateString(),
            'status' => $status,
            'created_by' => $this->admins['kristine'],
            'created_at' => $date,
            'updated_at' => $date,
        ]);
        DB::table('penalty_audit_logs')->insert([
            'penalty_id' => $id,
            'action' => 'created',
            'performed_by' => $this->admins['kristine'],
            'reason' => null,
            'created_at' => $date,
        ]);

        return $id;
    }

    /** Re-totals a bill after penalties were attached. Returns the new total. */
    private function refreshBillTotal(int $billId): float
    {
        $bill = DB::table('billing_statements')->where('id', $billId)->first();
        $pen = (float) DB::table('penalties')->where('billing_id', $billId)->where('status', 'active')->sum('amount');
        $total = $bill->base_rent + $bill->utilities_amount + $bill->wifi_amount + $pen;
        DB::table('billing_statements')->where('id', $billId)->update(['penalty_amount' => $pen, 'total_amount' => $total]);

        return (float) $total;
    }

    private function emailFor(string $first, string $last, bool $guardian = false): string
    {
        $slug = fn ($s) => preg_replace('/[^a-z]/', '', strtolower(\Illuminate\Support\Str::ascii($s)));
        $firstPart = $slug(explode(' ', $first)[0]);

        return $firstPart . '.' . $slug($last) . ($guardian ? '.parent' : '') . '@gmail.com';
    }

    /** Realistic-looking PH mobile numbers (Globe/Smart prefixes). */
    private function phone(int $seed): string
    {
        $prefixes = ['0917', '0918', '0927', '0939', '0945', '0956', '0966', '0998', '0908', '0995'];

        return $prefixes[$seed % count($prefixes)] . str_pad((string) ((($seed * 7919) + 1234567) % 10000000), 7, '0', STR_PAD_LEFT);
    }

    private function refNo(string $method): string
    {
        $this->refCounter++;

        return $method === 'gcash'
            ? '10' . str_pad((string) ($this->refCounter * 48271 % 100000000000), 11, '0', STR_PAD_LEFT)
            : 'BDO-' . date('ymd') . '-' . $this->refCounter;
    }

    /**
     * Builds the same filled-in, signed lease contract an applicant gets
     * from the real e-sign step (ApplicationController::signContract), so
     * "Signed Contract" in the admin panel shows the applicant's own details
     * and a signature instead of the blank template.
     */
    private function signedApplicationContract(array $app, Carbon $signedAt): string
    {
        $bed = Bed::with('room.floor')->find($app['bed_id']);
        $name = "{$app['first_name']} {$app['last_name']}";

        $pdf = Pdf::loadView('pdfs.lease-contract', [
            'fullName' => $name,
            'contactNumber' => $app['contact_number'],
            'email' => $app['email'],
            'emergencyContactName' => $app['emergency_contact_name'],
            'emergencyContactNumber' => $app['emergency_contact_number'],
            'emergencyContactRelation' => $app['emergency_contact_relation'],
            'floorLabel' => $bed?->room?->floor?->floor_number,
            'roomNo' => $bed?->room?->room_no,
            'bedLabel' => $bed?->bed_label,
            'monthlyRate' => $bed?->room?->perBedRate(),
            'moveInDate' => Carbon::parse($app['preferred_start_date'])->format('F j, Y'),
            'moveOutDate' => Carbon::parse($app['tenant_end_date'])->format('F j, Y'),
            'todayDate' => $signedAt->format('F j, Y'),
            'signatureDataUrl' => 'data:image/png;base64,' . base64_encode($this->demoSignature($name)),
        ]);

        $path = 'application-documents/signed-contracts/demo-' . Str::slug($name) . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /** A pen-like scribble on a transparent PNG, different for each name. */
    private function demoSignature(string $name): string
    {
        $w = 360;
        $h = 110;
        $img = imagecreatetruecolor($w, $h);
        imagesavealpha($img, true);
        imagefill($img, 0, 0, imagecolorallocatealpha($img, 0, 0, 0, 127));
        $ink = imagecolorallocate($img, 20, 40, 110);
        imagesetthickness($img, 3);

        mt_srand(crc32($name));
        $f1 = mt_rand(8, 14) / 100;
        $f2 = mt_rand(20, 35) / 100;
        $amp = mt_rand(18, 28);
        mt_srand();

        $prev = null;
        for ($x = 15; $x <= $w - 30; $x += 2) {
            $y = (int) ($h / 2 + $amp * sin($x * $f1) * cos($x * $f2) - ($x / $w) * 15);
            if ($prev) {
                imageline($img, $prev[0], $prev[1], $x, $y, $ink);
            }
            $prev = [$x, $y];
        }
        imageline($img, 40, $h - 22, $w - 50, $h - 30, $ink); // underline flourish

        ob_start();
        imagepng($img);
        imagedestroy($img);

        return ob_get_clean();
    }

    /**
     * Uses DEMO-ONLY placeholder images drawn here (never earlier test
     * uploads, which contained unrelated personal files) so "View ID" and
     * "View proof" links open something safe to show the panel. The signed
     * contract points at the dorm's blank contract template.
     */
    private function pickExistingFiles(): void
    {
        $disk = Storage::disk('public');
        $disk->put('demo/sample-valid-id.png', $this->demoImage('SAMPLE VALID ID', ['Name: (demo tenant)', 'ID No.: 0000-0000-0000', 'For defense demo only']));
        $disk->put('demo/sample-payment-proof.png', $this->demoImage('SAMPLE PAYMENT PROOF', ['Paid to: NEST.PH', 'Reference No.: see payment record', 'For defense demo only']));

        // The file Ana uploads live in Part 2 of the walkthrough.
        $anaAmount = number_format($this->anaMoveInFee(), 2);
        File::ensureDirectoryExists(base_path('docs/demo'));
        File::put(base_path('docs/demo/Ana_move-in_payment_proof.png'), $this->demoImage('PAYMENT PROOF - ANA', [
            'Paid to: NEST.PH', "Amount: PHP {$anaAmount}", 'Reference No.: 1029 384 756', 'Move-in fee (deposit + advance)', 'For defense demo only',
        ]));

        $this->files = [
            'id' => ['demo/sample-valid-id.png'],
            'contract' => $disk->exists('contracts/dormitory-contract.pdf') ? 'contracts/dormitory-contract.pdf' : null,
            'proof' => 'demo/sample-payment-proof.png',
        ];
    }

    /** A plain 800x500 PNG with a title and a few lines of text. */
    private function demoImage(string $title, array $lines): string
    {
        $img = imagecreatetruecolor(800, 500);
        $bg = imagecolorallocate($img, 245, 247, 246);
        $ink = imagecolorallocate($img, 15, 76, 92);
        $grey = imagecolorallocate($img, 90, 90, 90);
        $red = imagecolorallocate($img, 190, 40, 40);
        imagefill($img, 0, 0, $bg);
        imagerectangle($img, 10, 10, 789, 489, $ink);
        imagefilledrectangle($img, 10, 10, 789, 80, $ink);

        $font = 'C:/Windows/Fonts/arialbd.ttf';
        $ttf = function_exists('imagettftext') && is_file($font);
        $write = function ($size, $y, $color, $text) use ($img, $font, $ttf) {
            $ttf ? imagettftext($img, $size, 0, 40, $y, $color, $font, $text) : imagestring($img, 5, 40, $y - 15, $text, $color);
        };

        $write(26, 58, imagecolorallocate($img, 255, 255, 255), $title);
        foreach ($lines as $i => $line) {
            $write(18, 150 + $i * 50, $i === count($lines) - 1 ? $red : $grey, $line);
        }

        ob_start();
        imagepng($img);
        imagedestroy($img);

        return ob_get_clean();
    }
}
