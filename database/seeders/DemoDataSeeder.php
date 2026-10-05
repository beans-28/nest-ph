<?php

namespace Database\Seeders;

use App\Models\Bed;
use App\Models\DormitoryProfile;
use App\Models\RoomType;
use App\Models\Tenant;
use App\Models\VrScene;
use App\Services\EscalationService;
use App\Services\PanoramaFillService;
use App\Services\TenancyDocuments;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Capstone defense demo data, set up as our client dormitory:
 * Pureza Station Dormitory (329 C De Dios, Sta. Mesa, Manila).
 *
 * WIPES every tenant, admin, application, bill, payment, ticket, review,
 * announcement and inquiry, then fills the system with realistic Filipino
 * dummy data following Pureza's real documents:
 *   - its room types and rates (P3,800-P4,500 per bed; solo fan room
 *     P4,500 per room), on a ground floor + 2nd to 5th floors,
 *   - rent due on the 1st, a 3-day grace period, a one-time 10% late fee,
 *   - a 3-month minimum stay, move-out at the end of the month,
 *   - one month advance + one month deposit on move-in,
 *   - water, electricity and Wi-Fi included in the rent (not stated in the
 *     documents -- a realistic dummy choice; change it in Dormitory Profile),
 *   - its 25 house rules, other charges and GCash/BDO accounts
 *     (PurezaStationSeeder).
 * Current tenants have their own signed copies of Pureza's three documents.
 *
 * Kept: the owner account, rooms with VR tours (as rooms 101 and 102),
 * amenities. Everything else is rebuilt.
 *
 * Run with:  php artisan db:seed --class=DemoDataSeeder
 *
 * All dates are built around the defense day, DEFENSE_DATE (October 6,
 * 2026), whatever day it's actually run (set DEMO_DATE in .env to use
 * another day). Because rent is due on the 1st, that date matters:
 *   - run on the 1st-4th (within the grace period): this month's bills of
 *     the "unpaid / partial / proof" demo tenants are this month's,
 *     not yet overdue;
 *   - run later in the month: those demo bills are next month's (issued
 *     early), so they still show as "not yet due" instead of turning into
 *     extra delinquent accounts.
 * The overdue demo tenants (Ben, Camille, ...) always get exactly the
 * number of days overdue the walkthrough needs: their last bill's due date
 * is set back from the run date for that reason.
 *
 * Every account's password: Password123!
 */
class DemoDataSeeder extends Seeder
{
    private const PASSWORD = 'Password123!';

    /** Capstone defense day: the date all demo data is built around. */
    private const DEFENSE_DATE = '2026-10-06';

    private const OWNER_EMAIL = 'owner@nestph.test';

    /**
     * The SMS gateway (TextBee) is LIVE. The escalation engine automatically
     * texts overdue tenants and their emergency contacts, so every dummy
     * tenant who has an unpaid bill (and could become overdue) uses this
     * number instead of a made-up one -- any text goes to the team's own
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

    /** Pureza's documents took effect on this date; current tenants re-signed them then. */
    private const DOCUMENTS_EFFECTIVE = '2026-09-30';

    /** Beds left empty on purpose so the dorm isn't 100% full (vacant beds to apply for). */
    private const LEAVE_VACANT = ['101-4', '102-4', '105-1', '204-5', '204-6', '303-11', '303-12', '402-13', '402-14', '402-15', '402-16',
        '501-12', '501-13', '501-14'];

    /** Beds held by the pending applications (seedPendingApplications). */
    private const PENDING_APPLICATION_BEDS = ['202-2', '203-3', '301-3', '302-2'];

    private Carbon $today;

    private int $grace;

    /** True on the 1st-4th: this month's bills are still within the grace period. */
    private bool $inGrace;

    private int $ownerId;

    private array $admins = [];   // key => user id

    private array $beds = [];     // '101-1' => bed id

    private array $reviewPhotos = []; // set by seedDemoPhotos()
    private array $rooms = [];    // '101' => room row

    private array $files = [];    // reusable demo files

    private array $usedEmails = [];

    private int $refCounter = 1000;

    public function run(): void
    {
        // This seeder empties tables before filling them with demo data.
        // On the live site that would wipe real tenants and payments.
        // The only exception: the demo site, where DEMO_RESET_ON_DEPLOY=true says resetting is wanted.
        if (app()->environment('production') && ! config('app.demo_reset_on_deploy')) {
            throw new \RuntimeException('DemoDataSeeder refuses to run in production: it deletes existing data.');
        }

        // Every date is built around the defense day (not the day the seeder
        // runs), so the data looks right on October 6 even if it's seeded
        // earlier. "now()" is frozen at 8:00 AM that day while seeding, then
        // released. Override with DEMO_DATE=YYYY-MM-DD in .env if the date moves.
        Carbon::setTestNow(Carbon::parse(env('DEMO_DATE', self::DEFENSE_DATE))->setTime(8, 0));
        $this->today = now()->startOfDay();

        try {
            $this->seedEverything();
        } finally {
            Carbon::setTestNow();
        }

        $this->command?->info('Demo data seeded for Pureza Station Dormitory as of ' . $this->today->format('F j, Y') . '. Every password is ' . self::PASSWORD);
    }

    private function seedEverything(): void
    {
        // No wrapping transaction: MySQL's TRUNCATE can't be rolled back anyway.
        $this->wipe();
        $this->setUpDormitory();
        $this->pickExistingFiles();
        $this->seedAdmins();
        $this->seedFloorsRoomsBeds();
        $this->seedDemoPhotos();
        $this->seedTenants();
        $this->seedCurrentTenants();
        $this->seedFormerTenants();
        $this->seedTenantReports();
        $this->seedPendingApplications();
        $this->seedMonthlyExpenses();
        $this->seedInquiries();
        $this->seedAnnouncements();
        $this->seedTenantNotifications();
        $this->prepareVrDemo();
        $this->syncRoomStatuses();
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
    /*  1b. The dormitory itself                                           */
    /* ------------------------------------------------------------------ */

    /**
     * Pureza's real details (room types and rates, policy numbers, house
     * rules, other charges, GCash/BDO accounts) from its documents, plus
     * realistic dummy values where the documents say nothing.
     */
    private function setUpDormitory(): void
    {
        $this->call(PurezaStationSeeder::class);

        $profile = DormitoryProfile::current();
        $profile->fill([
            // Dummy: the documents leave the representative blank. The demo
            // owner account (Teresita "Ma'am Tess" Mendoza) signs for the dorm.
            'representative_name' => 'Teresita Mendoza',
            'representative_position' => 'Owner',
            // From the dorm's own contract letterhead.
            'contact_number' => '09774322155',
            // Dummy: the fee schedule leaves these boxes for the dorm to tick.
            // Flat per-bed rates with everything included is the common setup.
            'water_included' => true,
            'electricity_included' => true,
            'wifi_included' => true,
            'mid_month_move_in' => 'full',
        ]);
        if (blank($profile->description) || str_contains((string) $profile->description, 'NEST')) {
            $profile->description = 'Pureza Station Dormitory has been a trusted home for students and young professionals since 1996. '
                . 'Just a 5-minute walk from PUP and the Pureza LRT Station, we offer clean, secure, and affordable rooms designed for easy, comfortable living close to school and work.';
        }
        $profile->save();

        // Old test QRs are unlinked so tenants never scan the wrong one;
        // seedDemoPhotos() then attaches Pureza's own GCash QR.
        DB::table('payment_methods')->where('type', '!=', 'cash')->update(['qr_path' => null]);

        $this->grace = $profile->grace_period_days;
        $this->inGrace = $this->today->day <= 1 + $this->grace;
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
            // key, name, email, active, privileges
            ['kristine', 'Kristine Joy Bautista', 'kristine.bautista@nestph.test', true,
                ['manage_tenants', 'manage_rooms', 'manage_contracts', 'manage_billing', 'view_reports']],
            ['mark', 'Mark Anthony Villanueva', 'mark.villanueva@nestph.test', true,
                ['manage_billing', 'view_reports']],
            ['jerome', 'Jerome Castillo', 'jerome.castillo@nestph.test', true,
                ['manage_tenants', 'manage_rooms']],
            ['lea', 'Lea Mae Fernandez', 'lea.fernandez@nestph.test', false,
                []],
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
    /*  3. Floors, rooms, beds -- Pureza's room types                      */
    /* ------------------------------------------------------------------ */

    private function seedFloorsRoomsBeds(): void
    {
        $floorSpecs = [
            1 => ['Ground Floor', 'Lobby, receiving area and study hall. Solo fan rooms and 4-person AC rooms.'],
            2 => ['Second Floor', '4-person and 6-person air-conditioned rooms.'],
            3 => ['Third Floor', 'Air-conditioned rooms, including a 12-bed dorm room.'],
            4 => ['Fourth Floor', '6-person room and a 16-bed dorm room.'],
            5 => ['Fifth Floor', 'Large dorm room and a 4-person room with a view of the LRT line.'],
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
        foreach ($existingFloors as $extra) {
            DB::table('rooms')->where('floor_id', $extra)->update(['floor_id' => $floorIds[1]]);
            DB::table('floors')->where('id', $extra)->delete();
        }

        $fourAc = ['Air-conditioned', 'WiFi', 'Electricity', 'Water', 'Study table', 'Cabinet per bed'];
        $bigAc = ['Air-conditioned', 'WiFi', 'Electricity', 'Water', 'Double-deck beds', 'Lockers'];
        $fan = ['Electric fan', 'WiFi', 'Electricity', 'Water', 'Study table'];

        // room_no, floor, [room type name, location], beds, amenities, vr caption
        $g = 'Ground floor';
        $up = '2nd to 5th floor';
        $roomSpecs = [
            ['101', 1, ['Room with AC, 4 persons', $g], 4, $fourAc, 'Bright 4-person room beside the study hall'],
            ['102', 1, ['Room with AC, 4 persons', $g], 4, $fourAc, 'Quiet 4-person room with a computer corner'],
            ['103', 1, ['Room with AC, 4 persons', $g], 4, $fourAc, null],
            ['104', 1, ['Solo fan room', $g], 1, $fan, null],
            ['105', 1, ['Solo fan room', $g], 1, $fan, null],
            ['201', 2, ['Room with AC, 6 persons', $up], 6, $bigAc, null],
            ['202', 2, ['Room with AC, 4 persons', $up], 4, $fourAc, null],
            ['203', 2, ['Room with AC, 4 persons', $up], 4, $fourAc, null],
            ['204', 2, ['Room with AC, 6 persons', $up], 6, $bigAc, null],
            ['301', 3, ['Room with AC, 6 persons', $up], 6, $bigAc, null],
            ['302', 3, ['Room with AC, 4 persons', $up], 4, $fourAc, null],
            ['303', 3, ['Room with AC, 10–16 persons', $up], 12, $bigAc, null],
            ['304', 3, ['Room with AC, 4 persons', $up], 4, $fourAc, null],
            ['401', 4, ['Room with AC, 6 persons', $up], 6, $bigAc, null],
            ['402', 4, ['Room with AC, 10–16 persons', $up], 16, $bigAc, null],
            ['501', 5, ['Room with AC, 10–16 persons', $up], 14, $bigAc, null],
            ['502', 5, ['Room with AC, 4 persons', $up], 4, $fourAc, null],
        ];

        // Old rooms are reused (rooms with VR tours first); prepareVrDemo()
        // then moves the tour photos onto Room 105 for the walkthrough script.
        $existingRooms = DB::table('rooms')
            ->leftJoin('vr_scenes', 'vr_scenes.room_id', '=', 'rooms.id')
            ->groupBy('rooms.id')
            ->orderByRaw('COUNT(vr_scenes.id) > 0 DESC')
            ->orderBy('rooms.id')
            ->pluck('rooms.id')
            ->all();
        $keepIds = [];

        foreach ($roomSpecs as [$no, $floor, [$typeName, $typeLocation], $bedCount, $amen, $caption]) {
            $type = RoomType::where('name', $typeName)->where('location', $typeLocation)->firstOrFail();

            $data = [
                'floor_id' => $floorIds[$floor],
                'room_type_id' => $type->id,
                'room_no' => $no,
                'room_type' => $type->name,
                'amenities' => json_encode($amen),
                'monthly_rate' => $type->wholeRoomRate($bedCount),
                // Water, electricity and Wi-Fi are included in the rent.
                'monthly_utility_cost' => 0,
                'monthly_wifi_cost' => 0,
                'status' => $no === '104' ? 'maintenance' : 'available',
                'updated_at' => now(),
            ];

            if ($roomId = array_shift($existingRooms)) {
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

            $this->rooms[$no] = (object) ['id' => $roomId, 'beds' => $bedCount, 'rate' => $type->perBedRate($bedCount), 'type' => $type->name];

            for ($b = 1; $b <= $bedCount; $b++) {
                $status = ($no === '104' || ($no === '203' && $b === 4)) ? 'maintenance' : 'vacant';
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
     * Scenario keys (what the LATEST monthly bill looks like):
     *   paid            fully paid, nothing due
     *   unpaid          latest bill issued, not yet past the grace period
     *   partial         part of the latest bill paid in cash
     *   proof_pending   GCash proof uploaded, waiting for admin review
     *   proof_rejected  a proof was rejected, bill still unpaid
     *   overdue         overdue by `days` days after the grace period --
     *                   late fee and escalation ladder applied
     *   pending_movein  approved, has not paid move-in fees yet
     *                   (movein_half = paid the first half N days ago;
     *                    movein_half_proof / movein_expired for the follow-ups)
     *
     * k = months of (paid) billing history before the latest bill
     * L = moves the move-in day within its month (variety only)
     */
    private function tenantSpecs(): array
    {
        return [
            // ---- Room 101 (4-person AC, ground floor) ----
            ['Maria Angelica', 'Santos', 'female', '2004-03-14', 'student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila',
                'Brgy. San Isidro, Angono, Rizal', 'Rodelio Santos', 'Father', '101-1', 'paid', 'L' => 10, 'k' => 5,
                'review' => [5, 'Sobrang linis ng rooms and very accommodating ang staff. Five minutes lang lakad papuntang PUP. Highly recommended!']],
            ['Kimberly Anne', 'Dela Cruz', 'female', '2005-07-02', 'student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila',
                '123 Rizal St., Brgy. Poblacion, Tarlac City, Tarlac', 'Marites Dela Cruz', 'Mother', '101-2', 'unpaid', 'L' => 2, 'k' => 3],
            ['Patricia Mae', 'Gonzales', 'female', '2003-11-21', 'working_student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila',
                'Purok 4, Brgy. Maligaya, San Jose, Nueva Ecija', 'Lorna Gonzales', 'Mother', '101-3', 'proof_pending', 'L' => 4, 'k' => 2],
            ['Nicole Joy', 'Ramos', 'female', '2004-01-09', 'student', 'Technological University of the Philippines', 'Ayala Blvd., Ermita, Manila',
                'Blk 5 Lot 12, Villa Verde Subd., Dasmariñas, Cavite', 'Ernesto Ramos', 'Father', '301-4', 'paid', 'L' => 12, 'k' => 2,
                'lease' => 'expiring_soon'],

            // ---- Room 102 (4-person AC, ground floor) ----
            ['Juan Miguel', 'Reyes', 'male', '1999-05-30', 'full_time_employee', 'Accenture Philippines', 'Cyber One Bldg., Eastwood City, Quezon City',
                '45 Mabini St., Brgy. Poblacion, Lipa City, Batangas', 'Carmelita Reyes', 'Mother', '102-1', 'paid', 'L' => 15, 'k' => 6,
                'review' => [4, 'Maayos ang WiFi at tahimik sa gabi. Minsan lang medyo mahina ang tubig sa umaga pero agad naman inaayos.']],
            ['John Paul', 'Mendoza', 'male', '2004-08-17', 'student', 'Mapúa University', 'Muralla St., Intramuros, Manila',
                'Brgy. Bagong Silang, Lucena City, Quezon', 'Rosalie Mendoza', 'Mother', '303-4', 'overdue', 'L' => 14, 'k' => 3, 'days' => 30,
                'consent' => false],

            // ---- Room 103 (4-person AC, ground floor) ----
            ['Mark Joseph', 'Aquino', 'male', '2005-02-11', 'student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila',
                'Sitio Malinis, Brgy. San Roque, Antipolo City, Rizal', 'Josefina Aquino', 'Mother', '103-1', 'paid', 'L' => 5, 'k' => 1],
            ['Christian Dave', 'Torres', 'male', '2002-12-03', 'part_time_employee', 'Jollibee Foods Corp. (V. Mapa branch)', 'V. Mapa St., Sta. Mesa, Manila',
                '78 Bonifacio St., Brgy. Centro, Iriga City, Camarines Sur', 'Dante Torres', 'Father', '103-2', 'partial', 'L' => 3, 'k' => 4],
            // "Ben" in the walkthrough script: overdue at Stage 2, escalated live to Stage 6.
            // His emergency contact agreed to billing reminders, so Stage 4 really texts them.
            ['Benjamin', 'Robles', 'male', '2004-06-25', 'student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila',
                'Brgy. Santo Cristo, San Fernando, Pampanga', 'Evelyn Robles', 'Mother', '103-3', 'overdue', 'L' => 8, 'k' => 3, 'days' => 3,
                'sms' => self::BEN_SMS_NUMBER, 'consent' => true],
            ['Rafael Luis', 'Navarro', 'male', '2001-09-14', 'full_time_employee', 'BDO Unibank, Sta. Mesa Branch', 'Ramon Magsaysay Blvd., Sta. Mesa, Manila',
                '12 Aguinaldo Hwy., Brgy. Zapote, Bacoor, Cavite', 'Gloria Navarro', 'Mother', '304-1', 'paid', 'L' => 12, 'k' => 5,
                'lease' => 'expired'],

            // ---- Room 201 (6-person AC) ----
            ['Angela Marie', 'Villanueva', 'female', '1998-04-19', 'full_time_employee', 'Philippine General Hospital', 'Taft Ave., Ermita, Manila',
                'Brgy. Poblacion, Tuguegarao City, Cagayan', 'Arnel Villanueva', 'Father', '201-1', 'paid', 'L' => 20, 'k' => 8, 'discount' => 200,
                'review' => [5, 'Almost a year na ako dito. Safe, may curfew, at mabait si Ma\'am Tess. Perfect para sa mga nurse na shifting.']],
            ['Jasmine Rose', 'Garcia', 'female', '2005-10-05', 'student', 'Centro Escolar University', 'Mendiola St., San Miguel, Manila',
                'Purok 2, Brgy. Talon, Las Piñas City', 'Ramil Garcia', 'Father', '201-2', 'proof_rejected', 'L' => 1, 'k' => 2],
            ['Camille Louise', 'Flores', 'female', '2004-12-28', 'student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila',
                'Brgy. Mabini, Batangas City, Batangas', 'Susan Flores', 'Mother', '201-3', 'overdue', 'L' => 11, 'k' => 3, 'days' => 6, 'paused' => true],
            ['Bea Katrina', 'Pascual', 'female', '2003-05-16', 'student', 'University of the East', 'C.M. Recto Ave., Sampaloc, Manila',
                'Brgy. Sta. Rita, Olongapo City, Zambales', 'Gerardo Pascual', 'Father', '201-4', 'unpaid', 'L' => 0, 'k' => 3, 'damage' => true],
            ['Princess Joy', 'Manalo', 'female', '2002-08-08', 'working_student', 'Pamantasan ng Lungsod ng Maynila', 'General Luna St., Intramuros, Manila',
                'Brgy. Parian, Calamba City, Laguna', 'Cristina Manalo', 'Mother', '201-5', 'paid', 'L' => 7, 'k' => 3, 'unbilled_penalty' => true,
                'review' => [3, 'Okay naman overall. Sana lang may kitchen na pwedeng gamitin kasi bawal magluto sa room.']],
            ['Kathleen Mae', 'Salazar', 'female', '2006-01-30', 'student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila',
                'Brgy. Poblacion, Tagum City, Davao del Norte', 'Rogelio Salazar', 'Father', '201-6', 'pending_movein'],

            // ---- Room 202 (4-person AC) ----
            ['Carlo Miguel', 'Bautista', 'male', '2000-03-03', 'full_time_employee', 'Globe Telecom', 'The Globe Tower, BGC, Taguig City',
                'San Pablo City, Laguna', 'Nenita Bautista', 'Mother', '202-1', 'paid', 'L' => 9, 'k' => 2],

            // ---- Room 203 (4-person AC) ----
            ['Joshua Emmanuel', 'Lim', 'male', '2004-11-11', 'student', 'De La Salle University', 'Taft Ave., Malate, Manila',
                'Sta. Cruz, Laguna', 'Wilson Lim', 'Father', '203-1', 'paid', 'L' => 18, 'k' => 4,
                'review' => [4, 'Good value for money. Malinis ang CR at laging may tubig. Medyo strict sa visitors pero understandable.']],
            ['Paolo Andres', 'Ocampo', 'male', '2005-04-22', 'student', 'Adamson University', 'San Marcelino St., Ermita, Manila',
                'Brgy. Poblacion, Malolos City, Bulacan', 'Amelia Ocampo', 'Mother', '203-2', 'pending_movein', 'movein_proof' => true],

            // ---- Room 204 (6-person AC) ----
            ['Andrea Nicole', 'Tan', 'female', '1997-09-09', 'full_time_employee', 'SM Supermalls Corporate Office', 'Mall of Asia Complex, Pasay City',
                'Brgy. Kauswagan, Cagayan de Oro City', 'Rebecca Tan', 'Mother', '204-1', 'paid', 'L' => 6, 'k' => 5],

            // ---- Room 301 (6-person AC) ----
            ['Erika Jane', 'Morales', 'female', '2004-07-07', 'student', 'Far Eastern University', 'Nicanor Reyes St., Sampaloc, Manila',
                'Brgy. Pantal, Dagupan City, Pangasinan', 'Ronaldo Morales', 'Father', '301-1', 'paid', 'L' => 13, 'k' => 3],
            ['Hannah Grace', 'Soriano', 'female', '2006-02-14', 'student', 'Philippine Normal University', 'Taft Ave., Ermita, Manila',
                'Brgy. Dolores, Taytay, Rizal', 'Marilou Soriano', 'Mother', '301-2', 'paid', 'L' => 4, 'k' => 1],

            // ---- Former tenants ----
            ['Gabriel Jose', 'Rivera', 'male', '2001-06-01', 'full_time_employee', 'Meralco', 'Ortigas Ave., Pasig City',
                'Brgy. San Antonio, Biñan, Laguna', 'Imelda Rivera', 'Mother', '302-1', 'paid', 'L' => 70, 'k' => 4,
                'lease' => 'moved_out',
                'review' => [5, 'Nag-move out na ako kasi lumipat ang work ko, pero sobrang saya ng stay ko dito. Salamat Pureza Station!']],
            ['Joseph Allan', 'Cruz', 'male', '2003-03-27', 'student', 'Emilio Aguinaldo College', 'Gen. Malvar St., Malate, Manila',
                'Brgy. Tabing Ilog, Marilao, Bulacan', 'Nora Cruz', 'Mother', '303-1', 'overdue', 'L' => 50, 'k' => 4, 'days' => 95,
                'lease' => 'blacklisted'],

            // ---- More scenario tenants ----
            // Last in the list so they're processed after the former tenants of 302-1/303-1 free those beds, and so the scenario tenants' numbering (tickets, Juan's late fee) stays the same.
            ['Stephanie Claire', 'Uy', 'female', '2004-09-02', 'student', 'University of Santo Tomas', 'España Blvd., Sampaloc, Manila',
                'Brgy. Lourdes, Dagupan City, Pangasinan', 'Henry Uy', 'Father', '502-4', 'paid', 'L' => 16, 'k' => 7,
                'review' => [5, 'Ang ganda ng study hall sa baba, dito ako nagre-review lagi. Mabilis din sumagot ang admin sa tickets.']],
            ['Adrian Paul', 'Castro', 'male', '1999-12-12', 'full_time_employee', 'Teleperformance Philippines', 'Robinsons Cybergate, Mandaluyong City',
                'Brgy. Sampaloc, Tanauan City, Batangas', 'Leticia Castro', 'Mother', '102-2', 'paid', 'L' => 11, 'k' => 3],
            ['Vincent Ray', 'Magbanua', 'male', '2005-06-19', 'student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila',
                'Brgy. Poblacion, Roxas City, Capiz', 'Ramon Magbanua', 'Father', '103-4', 'paid', 'L' => 9, 'k' => 2],
            ['Luis Antonio', 'Del Rosario', 'male', '2003-02-27', 'working_student', 'University of the East', 'C.M. Recto Ave., Sampaloc, Manila',
                'Brgy. Cutcut, Angeles City, Pampanga', 'Rowena Del Rosario', 'Mother', '302-1', 'proof_pending', 'L' => 21, 'k' => 0],
            ['Jerome Anthony', 'Pineda', 'male', '2004-10-30', 'student', 'Mapúa University', 'Muralla St., Intramuros, Manila',
                'Brgy. Poblacion, Tarlac City, Tarlac', 'Grace Pineda', 'Mother', '303-1', 'paid', 'L' => 14, 'k' => 0],
            ['Kenneth Bryan', 'Sy', 'male', '2006-01-08', 'student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila',
                'Brgy. Balibago, Sta. Rosa City, Laguna', 'Victor Sy', 'Father', '303-5', 'unpaid', 'L' => 2, 'k' => 0],
            ['Emmanuel Jose', 'Villareal', 'male', '2002-07-21', 'part_time_employee', '7-Eleven (Legarda branch)', 'Legarda St., Sampaloc, Manila',
                'Brgy. San Vicente, Tacloban City, Leyte', 'Nelia Villareal', 'Mother', '303-6', 'paid', 'L' => 24, 'k' => 4],

            // ---- Escalation ladder: one tenant at each stage not covered above ----
            // (Ben = Stage 2, Camille = Stage 2 paused, John Paul = Stage 4 where the
            // tenant was notified instead of the emergency contact, Joseph = Stage 6 blacklisted.)
            // Stage 3: portal restricted, waiting for the emergency-contact step.
            ['Rowell Dominic', 'Lacsamana', 'male', '2003-08-29', 'student', 'Technological University of the Philippines', 'Ayala Blvd., Ermita, Manila',
                'Brgy. Poblacion, Pagsanjan, Laguna', 'Corazon Lacsamana', 'Mother', '401-1', 'overdue', 'L' => 6, 'k' => 2, 'days' => 14,
                'consent' => true],
            // Stage 5: emergency contact texted and a demand letter issued -- one step from blacklisting.
            ['Trisha Mae', 'Galvez', 'female', '2004-04-04', 'working_student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila',
                'Brgy. Sta. Lucia, San Fernando, Pampanga', 'Edgardo Galvez', 'Father', '401-2', 'overdue', 'L' => 10, 'k' => 3, 'days' => 75,
                'consent' => true],
            // Stage 1: just flagged overdue, no SMS reminder yet. "Dominic" in the walkthrough
            // script: escalated live from Stage 1 to Stage 6, so every SMS reaches the demo phone.
            ['Dominic James', 'Salvador', 'male', '2005-03-09', 'student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila',
                'Brgy. Poblacion, Gapan City, Nueva Ecija', 'Marissa Salvador', 'Mother', '401-6', 'overdue', 'L' => 5, 'k' => 2, 'days' => 1,
                'sms' => self::DEMO_SMS_NUMBER,
                'consent' => true],
            // Stage 3, emergency contact did NOT agree to billing reminders. Run the escalation
            // once and Stage 4 falls back to texting HER number and emailing her instead.
            ['Bianca Louise', 'Mercado', 'female', '2004-02-18', 'student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila',
                'Brgy. Poblacion, Lemery, Batangas', 'Reynaldo Mercado', 'Father', '402-1', 'overdue', 'L' => 7, 'k' => 2, 'days' => 20,
                'sms' => self::DEMO_SMS_NUMBER,
                'consent' => false],

            // ---- Half-paid move-in fees (Partial Payment = half; Fees Schedule 3.2: one-month reservation) ----
            // First half approved 26 days ago: reservation ends in about 5 days, so the admin bell shows
            // "half-paid move-in fee due within 7 days". Use "Skip past deadline" (Billing Testing Tools) to expire it.
            ['Jasmine Rose', 'Aquino', 'female', '2005-05-25', 'student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila',
                'Brgy. Poblacion, Calapan City, Oriental Mindoro', 'Teodoro Aquino', 'Father', '401-3', 'pending_movein', 'movein_half' => 26],
            // First half approved 12 days ago, second-half proof waiting for review: approving it activates him and sends the Move-In Permit.
            ['Mark Anthony', 'Villanueva', 'male', '2004-12-01', 'student', 'Technological University of the Philippines', 'Ayala Blvd., Ermita, Manila',
                'Brgy. San Roque, Antipolo City, Rizal', 'Josefina Villanueva', 'Mother', '401-4', 'pending_movein', 'movein_half' => 12, 'movein_half_proof' => true],
            // Paid only the first half 34 days ago and never the rest: the reservation already expired 2 days ago,
            // bed released, account closed, tenant emailed. Shows as "reservation expired" in the admin bell.
            ['Ella Mae', 'Fernandez', 'female', '2005-08-17', 'student', 'Far Eastern University', 'Nicanor Reyes St., Sampaloc, Manila',
                'Brgy. Bagumbayan, Naga City, Camarines Sur', 'Rodrigo Fernandez', 'Father', '401-5', 'pending_movein', 'movein_half' => 34, 'movein_expired' => true],
        ];
    }

    private function seedTenants(): void
    {
        $n = 0;

        foreach ($this->tenantSpecs() as $spec) {
            [$first, $last, $gender, $dob, $type, $school, $schoolAddr, $home, $ecName, $ecRel, $bedKey, $scenario] = $spec;
            $n++;

            $room = $this->rooms[explode('-', $bedKey)[0]];
            $bedId = $this->beds[$bedKey];
            $lease = $spec['lease'] ?? null;
            $k = $spec['k'] ?? 0;
            $isOverdue = $scenario === 'overdue';
            $mayGoOverdue = in_array($scenario, ['overdue', 'unpaid', 'partial', 'proof_pending', 'proof_rejected'], true);

            // The month of the latest monthly bill, and (for overdue demos) its due date.
            $overdueDue = $isOverdue ? $this->today->copy()->subDays($spec['days'] + $this->grace) : null;
            $lastMonth = match (true) {
                $scenario === 'pending_movein' => null,
                $isOverdue => $overdueDue->copy()->startOfMonth(),
                $lease === 'moved_out' => $this->today->copy()->subDays($spec['L'])->startOfMonth(),
                $scenario === 'paid' => $this->today->copy()->startOfMonth(),
                default => $this->currentScenarioMonth(),
            };

            // Move-in: the advance rent covers the move-in month, so the
            // first monthly bill is the month after. k+1 bills in total.
            $start = $scenario === 'pending_movein'
                ? $this->today->copy()->addDays(3 + $n % 3)
                : $lastMonth->copy()->subMonthsNoOverflow($k + 1)->addDays(($spec['L'] ?? 0) % 27)->min($this->today->copy()->subDays(2));

            $minEnd = DormitoryProfile::current()->minimumEndDate($start);
            $end = match ($lease) {
                // Ends this month if at least a week is left, otherwise next month (always within the 30-day "expiring soon" window or close to it).
                'expiring_soon' => $this->today->diffInDays($this->today->copy()->endOfMonth()) >= 7
                    ? $this->today->copy()->endOfMonth()->startOfDay()
                    : $this->today->copy()->addMonthNoOverflow()->endOfMonth()->startOfDay(),
                'expired' => $this->today->copy()->subMonthNoOverflow()->endOfMonth()->startOfDay(),
                'moved_out' => $lastMonth->copy()->endOfMonth()->startOfDay(),
                default => max($minEnd, $start->copy()->addMonthsNoOverflow(max(6, $k + 6))->endOfMonth()->startOfDay()),
            };

            $email = $this->emailFor($first, $last);
            $phone = $spec['sms'] ?? ($mayGoOverdue ? self::DEMO_SMS_NUMBER : $this->phone($n));
            $ecPhone = $spec['sms'] ?? ($mayGoOverdue ? self::DEMO_SMS_NUMBER : $this->phone($n + 100));
            $consent = $spec['consent'] ?? ($n % 3 !== 0);
            $appliedAt = $start->copy()->subDays(10 + $n % 5)->setTime(9 + $n % 8, 15);

            $tenantStatus = match (true) {
                $scenario === 'pending_movein' => 'pending_move_in_payment',
                $lease === 'moved_out' => 'inactive',
                default => 'active',
            };

            $ids = $this->createTenant([
                'first' => $first, 'last' => $last, 'gender' => $gender, 'dob' => $dob, 'type' => $type,
                'school' => $school, 'school_address' => $schoolAddr, 'home' => $home,
                'ec_name' => $ecName, 'ec_relation' => $ecRel, 'ec_phone' => $ecPhone, 'consent' => $consent,
                'email' => $email, 'phone' => $phone, 'bed_key' => $bedKey, 'room' => $room,
                'start' => $start, 'end' => $end, 'applied_at' => $appliedAt, 'n' => $n,
                'status' => $tenantStatus, 'user_active' => $lease !== 'moved_out',
                'medical' => $n % 6 === 0 ? 'Asthma (mild, with inhaler)' : 'None',
                'deactivation' => $lease === 'moved_out'
                    ? ['Moved out at the end of contract -- transferred to a job in Laguna.', $end->copy()->addDay(), $this->admins['kristine']]
                    : null,
                'blacklisted' => $lease === 'blacklisted',
                'portal_restricted' => $isOverdue && $spec['days'] >= EscalationService::STAGE_DAYS[3],
                'paused' => ! empty($spec['paused']),
                'discount' => $spec['discount'] ?? 0,
                // A moved-out tenant's contract is terminated, the same as the
                // real "Deactivate tenant" flow (TenantController::setStatus).
                'contract_status' => match ($lease) {
                    'expiring_soon' => 'expiring_soon',
                    'expired' => 'expired',
                    'moved_out', 'blacklisted' => 'terminated',
                    default => 'active',
                },
                'termination' => match ($lease) {
                    'blacklisted' => ['Terminated for non-payment after full delinquency escalation (Stage 6).', $this->today->copy()->subDays(30)],
                    'moved_out' => ['Moved out at the end of contract -- transferred to a job in Laguna.', $end->copy()->addDay()->setTime(10, 0)],
                    default => null,
                },
                // Current tenants hold signed copies of Pureza's documents.
                'signed_packet' => ! in_array($lease, ['moved_out', 'blacklisted'], true),
                'bed_status' => match (true) {
                    $scenario === 'pending_movein' => 'reserved',
                    in_array($lease, ['moved_out', 'blacklisted'], true) => 'vacant',
                    default => 'occupied',
                },
                'move_in_paid' => $scenario !== 'pending_movein',
            ]);
            [$tenantId, $contractId, $rate, $moveInBillId] = $ids;

            if ($scenario === 'pending_movein' && ! empty($spec['movein_half'])) {
                $this->halfPaidMoveIn($spec, $tenantId, $contractId, $bedId, $moveInBillId, $rate);
                continue;
            }

            if ($scenario === 'pending_movein') {
                if (! empty($spec['movein_proof'])) {
                    $this->payment($moveInBillId, $tenantId, $rate * 2, 'gcash', $this->today->copy()->subDay(), 'pending',
                        'Move-in fee (deposit + advance). Sent via GCash po.');
                }
                continue;
            }

            // Monthly bills: calendar months, due on the 1st.
            $month = $start->copy()->addMonthNoOverflow()->startOfMonth();
            $i = 0;
            while ($month->lte($lastMonth)) {
                $isLast = $month->isSameMonth($lastMonth);
                $state = $isLast ? $scenario : 'paid';
                $due = ($isLast && $isOverdue) ? $overdueDue->copy() : $month->copy();

                $billId = $this->monthlyBill($contractId, $tenantId, $rate, $month, $due,
                    $isLast && $month->gt($this->today) ? $this->today->copy() : null);

                if ($state === 'paid') {
                    // A few bills were paid late, with the one-time 10% late fee
                    // (Juan's third bill always is -- shown in his history).
                    $late = ($n === 5 && $i === 2) || ($n + $i) % 11 === 0;
                    $this->settle($billId, $tenantId, $rate, $due, $late ? 'late' : 'on_time', $n + $i,
                        $late && $n === 5 ? 'Sorry po late, na-delay sweldo.' : null);
                } else {
                    $this->applyScenario($state, $spec, $n, $tenantId, $billId, $bedId, $room, $rate, $due);
                }

                $month->addMonthNoOverflow();
                $i++;
            }

            if ($lease === 'moved_out') {
                $this->depositRefund($tenantId, $rate, $end, $n);
            }

            if (! empty($spec['unbilled_penalty'])) {
                // Not on any bill yet -- demo "attach penalties" / next generated bill picking it up.
                $this->penalty($tenantId, null, 'manual', 'Possession or use of hazardous items (Rules, item 9): butane stove found in room', 500,
                    $this->today->copy()->subDays(2), 'active');
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

    /**
     * The month the "unpaid / partial / proof" demo bills belong to: this
     * month while it's still within the grace period (1st-4th), otherwise
     * next month's bill, issued early -- so these demos never turn into
     * extra delinquent accounts no matter which day the seeder runs.
     */
    private function currentScenarioMonth(): Carbon
    {
        return $this->inGrace
            ? $this->today->copy()->startOfMonth()
            : $this->today->copy()->addMonthNoOverflow()->startOfMonth();
    }

    /* ------------------------------------------------------------------ */
    /*  4b. More current tenants -- fill the dorm to a realistic level     */
    /* ------------------------------------------------------------------ */

    /**
     * Fills the remaining beds (except LEAVE_VACANT, beds under
     * maintenance, and beds held by pending applications) with current
     * tenants, so occupancy looks like a real, busy dorm near PUP.
     */
    private function seedCurrentTenants(): void
    {
        mt_srand(20261001);

        $taken = DB::table('lease_contracts')->whereIn('status', ['active', 'expiring_soon', 'expired', 'pending'])->pluck('bed_id')->flip();
        $skip = array_merge(self::LEAVE_VACANT, self::PENDING_APPLICATION_BEDS, ['203-4']);

        $people = $this->namePool(['Althea', 'Bernadette', 'Charmaine', 'Dianne', 'Elaine', 'Faith', 'Gela', 'Hershey', 'Isabelle', 'Jamaica',
            'Krizia', 'Lovely', 'Maureen', 'Nathalie', 'Odessa', 'Pauline', 'Rhea', 'Shaira', 'Trisha', 'Venice',
            'Aaron', 'Bryle', 'Carl', 'Darwin', 'Earl', 'Froilan', 'Gerald', 'Harvey', 'Ian', 'Jhon', 'Kurt', 'Lester',
            'Mico', 'Noel', 'Owen', 'Patrick', 'Reymart', 'Sean', 'Tristan', 'Wendell'],
            ['Alcantara', 'Bernardo', 'Cabrera', 'Dimaculangan', 'Espiritu', 'Francisco', 'Gatchalian', 'Hidalgo', 'Ignacio',
                'Jimenez', 'Lagman', 'Mangubat', 'Natividad', 'Obispo', 'Padilla', 'Quinto', 'Romualdez', 'Sarmiento',
                'Tagle', 'Ventura', 'Yambao', 'Zaragoza', 'Aguilar', 'Bonifacio', 'Catapang', 'Delos Reyes', 'Evangelista']);

        $schools = [
            ['student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila'],
            ['student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila'],
            ['student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila'],
            ['student', 'University of the East', 'C.M. Recto Ave., Sampaloc, Manila'],
            ['student', 'Far Eastern University', 'Nicanor Reyes St., Sampaloc, Manila'],
            ['student', 'University of Santo Tomas', 'España Blvd., Sampaloc, Manila'],
            ['working_student', 'Polytechnic University of the Philippines', 'Anonas St., Sta. Mesa, Manila'],
            ['full_time_employee', 'SM Megamall Corporate Office', 'Ortigas Center, Mandaluyong City'],
            ['full_time_employee', 'Concentrix (Eton Centris)', 'EDSA cor. Quezon Ave., Quezon City'],
            ['part_time_employee', 'Starbucks Coffee (V. Mapa)', 'V. Mapa St., Sta. Mesa, Manila'],
        ];
        $homes = ['Brgy. Poblacion, Batangas City, Batangas', 'Brgy. San Jose, Tarlac City, Tarlac', 'Brgy. Centro, Naga City, Camarines Sur',
            'Brgy. Malabanias, Angeles City, Pampanga', 'Brgy. Bagumbayan, Lucena City, Quezon', 'Brgy. Tagapo, Sta. Rosa City, Laguna',
            'Brgy. Sto. Niño, San Fernando, La Union', 'Brgy. Poblacion, Calapan City, Oriental Mindoro', 'Brgy. Bayanan, Bacoor, Cavite',
            'Brgy. Sampaloc, Cabanatuan City, Nueva Ecija'];
        $parents = ['Teresa', 'Roberto', 'Lourdes', 'Ricardo', 'Elena', 'Manuel', 'Gemma', 'Arturo', 'Rosario', 'Danilo'];

        $n = 500;
        $made = 0;
        foreach ($this->beds as $bedKey => $bedId) {
            if (isset($taken[$bedId]) || in_array($bedKey, $skip, true) || str_starts_with($bedKey, '104-')) {
                continue;
            }
            [$first, $last, $gender] = array_shift($people);
            $n++;
            $made++;

            [$type, $school, $schoolAddr] = $schools[$n % count($schools)];
            $room = $this->rooms[explode('-', $bedKey)[0]];
            $monthsAgo = mt_rand(1, 11);
            $start = $this->today->copy()->startOfMonth()->subMonthsNoOverflow($monthsAgo)->addDays(mt_rand(0, 26));
            $minEnd = DormitoryProfile::current()->minimumEndDate($start);
            $end = max($minEnd, $this->today->copy()->addMonthsNoOverflow(mt_rand(1, 7))->endOfMonth()->startOfDay());

            // This month's bill: on the 1st-4th about a third haven't paid yet
            // (still within the grace period); later in the month all have.
            $currentUnpaid = $this->inGrace && mt_rand(1, 100) <= 35;
            $phone = $currentUnpaid ? self::DEMO_SMS_NUMBER : $this->phone($n);
            $consent = mt_rand(1, 100) <= 70;
            $ecName = $parents[$n % count($parents)] . ' ' . $last;

            [$tenantId, $contractId, $rate] = $this->createTenant([
                'first' => $first, 'last' => $last, 'gender' => $gender,
                'dob' => $this->today->copy()->subYears(str_contains($type, 'employee') ? mt_rand(23, 30) : mt_rand(18, 22))->subDays(mt_rand(0, 360))->toDateString(),
                'type' => $type, 'school' => $school, 'school_address' => $schoolAddr, 'home' => $homes[$n % count($homes)],
                'ec_name' => $ecName, 'ec_relation' => $n % 2 ? 'Mother' : 'Father',
                'ec_phone' => $currentUnpaid ? self::DEMO_SMS_NUMBER : $this->phone($n + 100), 'consent' => $consent,
                'email' => $this->emailFor($first, $last), 'phone' => $phone, 'bed_key' => $bedKey, 'room' => $room,
                'start' => $start, 'end' => $end, 'applied_at' => $start->copy()->subDays(7 + $n % 10)->setTime(9 + $n % 8, 40), 'n' => $n,
                'status' => 'active', 'user_active' => true, 'medical' => 'None', 'deactivation' => null,
                'blacklisted' => false, 'portal_restricted' => false, 'paused' => false, 'discount' => 0,
                'contract_status' => $end->lte($this->today->copy()->addDays(30)) ? 'expiring_soon' : 'active',
                'termination' => null, 'signed_packet' => true, 'bed_status' => 'occupied', 'move_in_paid' => true,
            ]);

            $month = $start->copy()->addMonthNoOverflow()->startOfMonth();
            $i = 0;
            while ($month->lte($this->today)) {
                $billId = $this->monthlyBill($contractId, $tenantId, $rate, $month, $month->copy());
                $isCurrent = $month->isSameMonth($this->today);
                if (! ($isCurrent && $currentUnpaid)) {
                    $this->settle($billId, $tenantId, $rate, $month->copy(), mt_rand(1, 100) <= 7 && ! $isCurrent ? 'late' : 'on_time', $n + $i);
                }
                $month->addMonthNoOverflow();
                $i++;
            }
        }

        $this->command?->info("Seeded {$made} more current tenants.");
    }

    /* ------------------------------------------------------------------ */
    /*  4c. Former tenants -- 13 months of move-ins and move-outs          */
    /* ------------------------------------------------------------------ */

    /**
     * Fills the past year with tenants who already moved out, so the
     * Occupancy Trend chart and the Financial report have real history
     * to show. Each bed gets back-to-back stays of 3-8 months (Pureza's
     * 3-month minimum, moving out at the end of a month, as its documents
     * require) that all end BEFORE that bed's current tenant moved in.
     *
     * Each former tenant gets the same records a real move-out leaves
     * behind: inactive account, terminated contract, paid bills and
     * payments, and a deposit refund within 21 days.
     */
    private function seedFormerTenants(): void
    {
        mt_srand(20260930); // same "random" history every time the seeder runs

        $people = $this->namePool(['Aldrin', 'Bianca', 'Cedric', 'Danica', 'Elijah', 'Francine', 'Gian', 'Hazel', 'Ivan', 'Janelle',
            'Kevin', 'Lianne', 'Marco', 'Nina', 'Oliver', 'Pia', 'Queenie', 'Renz', 'Sofia', 'Troy', 'Vanessa',
            'Warren', 'Ysabel', 'Zach', 'Alyssa', 'Bryan', 'Clarisse', 'Dominic', 'Ella', 'Franco', 'Gwen', 'Harold', 'Irish',
            'Jericho', 'Kyla', 'Lance', 'Mika', 'Nathan', 'Rica'],
            ['Abad', 'Buenaventura', 'Castillo', 'Dizon', 'Enriquez', 'Fernandez', 'Galang', 'Hernandez', 'Ilagan',
                'Javier', 'Lacson', 'Macaraeg', 'Nepomuceno', 'Ortega', 'Panganiban', 'Quiambao', 'Rosales', 'Sison', 'Tolentino',
                'Umali', 'Valdez', 'Yap', 'Zamora', 'Agustin', 'Belmonte', 'Cordero', 'Domingo', 'Estrada', 'Figueroa', 'Guevarra']);
        $reasons = [
            'Graduated -- moved back home to the province.',
            'Moved out at the end of contract.',
            'Transferred to a dorm closer to the new campus.',
            'Found a job in another city.',
            'Moved in with relatives in Quezon City.',
            'Semester ended; will not be renewing.',
        ];
        $types = [['student', 'Student'], ['student', 'Student'], ['working_student', 'Student'], ['full_time_employee', 'Employee']];
        $schools = ['Polytechnic University of the Philippines', 'University of the East', 'Far Eastern University',
            'University of Santo Tomas', 'Centro Escolar University', 'National University', 'Accenture Philippines', 'Concentrix Manila'];

        $historyStart = $this->today->copy()->subMonthsNoOverflow(13)->startOfMonth();
        $n = 200;
        $made = 0;

        foreach ($this->beds as $bedKey => $bedId) {
            [$roomNo] = explode('-', $bedKey);
            if ($roomNo === '104' || $bedKey === '203-4') {
                continue; // under maintenance
            }
            $room = $this->rooms[$roomNo];

            // Former stays must end before the bed's current/next tenant starts.
            $nextStart = DB::table('lease_contracts')->where('bed_id', $bedId)->min('start_date');
            $limit = $nextStart
                ? Carbon::parse($nextStart)->subDays(5)
                : $this->today->copy()->subDays(mt_rand(15, 240)); // empty now: last tenant left sometime this year

            $cursor = $historyStart->copy()->addDays(mt_rand(0, 45));
            while (true) {
                // At least 3 months, moving out on the last day of a month.
                $moveOut = $cursor->copy()->addMonthsNoOverflow(mt_rand(3, 8))->subDay()->endOfMonth()->startOfDay();
                if ($moveOut->gt($limit)) {
                    $moveOut = $limit->copy()->subMonthNoOverflow()->endOfMonth()->startOfDay();
                    if ($moveOut->lt(DormitoryProfile::current()->minimumEndDate($cursor))) {
                        break;
                    }
                }

                [$first, $last, $gender] = array_shift($people) ?? [null, null, null];
                if (! $first) {
                    break;
                }

                $n++;
                $made++;
                [$type, $occupation] = $types[$n % count($types)];
                $this->formerTenant($first, $last, $gender, $type, $occupation, $schools[$n % count($schools)],
                    $reasons[$n % count($reasons)], $bedKey, $room, $cursor, $moveOut, $n);

                $cursor = $moveOut->copy()->addDays(mt_rand(3, 45));
                if ($cursor->gte($limit)) {
                    break;
                }
            }
        }

        $this->command?->info("Seeded {$made} former (moved-out) tenants.");
    }

    private function formerTenant(string $first, string $last, string $gender, string $type, string $occupation, string $school,
        string $reason, string $bedKey, object $room, Carbon $start, Carbon $moveOut, int $n): void
    {
        $home = ['Brgy. Poblacion, Batangas City', 'Brgy. San Jose, Tarlac City', 'Brgy. Centro, Naga City',
            'Brgy. Malabanias, Angeles City', 'Brgy. Bagumbayan, Lucena City'][$n % 5];

        [$tenantId, $contractId, $rate] = $this->createTenant([
            'first' => $first, 'last' => $last, 'gender' => $gender,
            'dob' => $this->today->copy()->subYears($type === 'full_time_employee' ? 25 : 20)->subDays($n * 13 % 300)->toDateString(),
            'type' => $type, 'school' => $school, 'school_address' => 'Manila', 'home' => $home,
            'ec_name' => (['Teresa', 'Roberto', 'Lourdes', 'Ricardo', 'Elena', 'Manuel'][$n % 6]) . ' ' . $last,
            'ec_relation' => ['Mother', 'Father'][$n % 2], 'ec_phone' => $this->phone($n + 100), 'consent' => false,
            'email' => $this->emailFor($first, $last), 'phone' => $this->phone($n), 'bed_key' => $bedKey, 'room' => $room,
            'start' => $start, 'end' => $moveOut, 'applied_at' => $start->copy()->subDays(10 + $n % 7)->setTime(9 + $n % 8, 30), 'n' => $n,
            'status' => 'inactive', 'user_active' => false, 'medical' => 'None',
            'deactivation' => [$reason, $moveOut->copy()->setTime(10, 0), $this->admins['kristine']],
            'blacklisted' => false, 'portal_restricted' => false, 'paused' => false, 'discount' => 0,
            // Same result as the real "Deactivate tenant" flow (TenantController::setStatus).
            'contract_status' => 'terminated', 'termination' => [$reason, $moveOut->copy()->setTime(10, 0)],
            'signed_packet' => false, 'bed_status' => null, 'move_in_paid' => true, 'occupation' => $occupation,
        ]);

        $month = $start->copy()->addMonthNoOverflow()->startOfMonth();
        $i = 0;
        while ($month->lte($moveOut)) {
            $billId = $this->monthlyBill($contractId, $tenantId, $rate, $month, $month->copy());
            $this->settle($billId, $tenantId, $rate, $month->copy(), ($n + $i) % 9 === 0 ? 'late' : 'on_time', $n + $i);
            $month->addMonthNoOverflow();
            $i++;
        }

        $this->depositRefund($tenantId, $rate, $moveOut, $n);
    }

    /* ------------------------------------------------------------------ */
    /*  Shared: one tenant's account, application, contract, move-in fee   */
    /* ------------------------------------------------------------------ */

    /**
     * Creates the login, tenant record, approved application, lease contract
     * and move-in fee (1 month advance + 1 month deposit) for one stay.
     * Returns [tenant id, contract id, monthly rate, move-in bill id].
     */
    private function createTenant(array $t): array
    {
        $tenantRole = DB::table('roles')->where('role_name', 'tenant')->value('id');
        static $password = null;
        $password ??= Hash::make(self::PASSWORD);

        $bedId = $this->beds[$t['bed_key']];
        $room = $t['room'];
        $start = $t['start'];
        $end = $t['end'];
        $appliedAt = $t['applied_at'];
        $rate = round($room->rate - ($t['discount'] ?? 0), 2);
        $this->usedEmails[$t['email']] = true;

        // Current tenants re-signed Pureza's new documents when they took
        // effect (or signed them on applying, if that was later). Former
        // tenants left before them, so they keep the dorm's old contract.
        $signedAt = $appliedAt->copy()->addDay();
        $packet = null;
        if ($t['signed_packet']) {
            $packetSignedAt = $signedAt->copy()->max(Carbon::parse(self::DOCUMENTS_EFFECTIVE)->setTime(10, 0));
            if ($packetSignedAt->gt(now())) {
                $packetSignedAt = $signedAt->copy();
            }
            $packet = $this->signedPacket([
                'first_name' => $t['first'], 'last_name' => $t['last'], 'home_address' => $t['home'],
                'contact_number' => $t['phone'], 'email' => $t['email'],
                'emergency_contact_name' => $t['ec_name'], 'emergency_contact_relation' => $t['ec_relation'],
                'emergency_contact_number' => $t['ec_phone'], 'emergency_billing_consent' => $t['consent'],
                'preferred_start_date' => $start->toDateString(), 'tenant_end_date' => $end->toDateString(),
            ], $bedId, $packetSignedAt, $t['ec_name']);
        }
        $applicationContract = $packet && $signedAt->gte(Carbon::parse(self::DOCUMENTS_EFFECTIVE)) ? $packet : $this->files['contract'];

        $userId = DB::table('users')->insertGetId([
            'name' => "{$t['first']} {$t['last']}",
            'email' => $t['email'],
            'password' => $password,
            'role_id' => $tenantRole,
            'is_active' => $t['user_active'],
            'created_at' => $appliedAt->copy()->addDays(2),
            'updated_at' => $appliedAt->copy()->addDays(2),
        ]);

        $idDoc = $this->files['id'][0];

        $tenantId = DB::table('tenants')->insertGetId([
            'user_id' => $userId,
            'first_name' => $t['first'],
            'last_name' => $t['last'],
            'contact_number' => $t['phone'],
            'email' => $t['email'],
            'emergency_contact_name' => $t['ec_name'],
            'emergency_contact_number' => $t['ec_phone'],
            'emergency_billing_reminders' => $t['consent'],
            'date_of_birth' => $t['dob'],
            'home_address' => $t['home'],
            'tenant_type' => $t['type'],
            'id_document_path' => $idDoc,
            'emergency_contact_id_path' => $this->files['emergency_id'],
            'signed_contract_path' => $packet ?? $this->files['contract'],
            'status' => $t['status'],
            'deactivation_reason' => $t['deactivation'][0] ?? null,
            'deactivated_at' => $t['deactivation'][1] ?? null,
            'deactivated_by' => $t['deactivation'][2] ?? null,
            'is_blacklisted' => $t['blacklisted'],
            'portal_restricted' => $t['portal_restricted'],
            'escalation_paused' => $t['paused'],
            'created_at' => $appliedAt->copy()->addDays(2),
            'updated_at' => now(),
        ]);

        $applicationId = DB::table('applications')->insertGetId([
            'tenant_id' => $tenantId,
            'first_name' => $t['first'],
            'last_name' => $t['last'],
            'birthdate' => $t['dob'],
            'gender' => $t['gender'],
            'nationality' => 'Filipino',
            'medical_condition' => $t['medical'],
            'occupation' => $t['occupation'] ?? (str_contains($t['type'], 'student') ? 'Student' : 'Employee'),
            'school_company' => $t['school'],
            'school_company_address' => $t['school_address'],
            'contact_number' => $t['phone'],
            'email' => $t['email'],
            'home_address' => $t['home'],
            'emergency_contact_name' => $t['ec_name'],
            'emergency_contact_number' => $t['ec_phone'],
            'emergency_contact_email' => $this->emailFor(explode(' ', $t['ec_name'])[0], $t['last'], true),
            'emergency_contact_relation' => $t['ec_relation'],
            'emergency_contact_signed' => $applicationContract === $packet && $packet !== null,
            'emergency_billing_consent' => $applicationContract === $packet && $packet !== null && $t['consent'],
            'bed_id' => $bedId,
            'preferred_start_date' => $start->toDateString(),
            'tenant_end_date' => $end->toDateString(),
            'type_of_tenant' => $t['type'],
            'id_document_path' => $idDoc,
            'emergency_contact_id_path' => $this->files['emergency_id'],
            'signed_contract_path' => $applicationContract,
            'dpa_consent' => true,
            'status' => 'approved',
            'approved_by' => $this->admins['kristine'],
            'created_at' => $appliedAt,
            'updated_at' => $appliedAt->copy()->addDays(2),
        ]);

        $contractId = DB::table('lease_contracts')->insertGetId([
            'application_id' => $applicationId,
            'tenant_id' => $tenantId,
            'bed_id' => $bedId,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'monthly_rate' => $rate,
            'discount_amount' => ($t['discount'] ?? 0) ?: null,
            'esign_status' => 'signed',
            'signed_document_url' => $packet ?? $this->files['contract'],
            'signed_at' => $signedAt,
            'status' => $t['contract_status'],
            'termination_reason' => $t['termination'][0] ?? null,
            'terminated_at' => $t['termination'][1] ?? null,
            'created_by' => $this->admins['kristine'],
            'approved_by' => $this->admins['kristine'],
            'created_at' => $appliedAt->copy()->addDays(2),
            'updated_at' => now(),
        ]);

        if ($t['bed_status']) {
            DB::table('beds')->where('id', $bedId)->update(['status' => $t['bed_status']]);
        }

        // Move-in fee: 1 month advance rent (covers the move-in month) + 1 month deposit.
        $moveIn = $rate * 2;
        $moveInBillId = DB::table('billing_statements')->insertGetId([
            'contract_id' => $contractId,
            'tenant_id' => $tenantId,
            'type' => 'move_in',
            'billing_period_start' => $start->toDateString(),
            'billing_period_end' => $start->toDateString(),
            'due_date' => $start->toDateString(),
            'base_rent' => $moveIn,
            'utilities_amount' => 0,
            'wifi_amount' => 0,
            'penalty_amount' => 0,
            'total_amount' => $moveIn,
            'status' => $t['move_in_paid'] ? 'paid' : 'unpaid',
            'created_at' => $appliedAt->copy()->addDays(2),
            'updated_at' => now(),
        ]);
        if ($t['move_in_paid']) {
            $this->payment($moveInBillId, $tenantId, $moveIn, 'cash', $start->copy()->subDay(), 'approved',
                'Move-in fee (1 month advance + 1 month deposit) paid at the admin office.');
        }

        return [$tenantId, $contractId, $rate, $moveInBillId];
    }

    /**
     * A move-in fee paid by halves (Partial Payment). The first half was
     * approved `movein_half` days ago; the reservation lasts one month from
     * that payment (ExpireMoveInReservations).
     */
    private function halfPaidMoveIn(array $spec, int $tenantId, int $contractId, int $bedId, int $billId, float $rate): void
    {
        $half = round($rate, 2); // move-in fee is 2 x rate, so half is one month's rate
        $paidOn = $this->today->copy()->subDays($spec['movein_half'])->setTime(14, 20);

        $this->payment($billId, $tenantId, $half, 'gcash', $paidOn, 'approved',
            'First half of the move-in fee po. Second half next month.', 'Verified: first half of the move-in fee.');
        DB::table('billing_statements')->where('id', $billId)->update(['status' => 'partial']);

        if (! empty($spec['movein_half_proof'])) {
            $this->payment($billId, $tenantId, $half, 'gcash', $this->today->copy()->subDay()->setTime(19, 5), 'pending',
                'Second half of the move-in fee. Thank you po!');
        }

        if (! empty($spec['movein_expired'])) {
            $expiredAt = $paidOn->copy()->startOfDay()->addMonthNoOverflow()->addDay()->setTime(0, 5);
            DB::table('billing_statements')->where('id', $billId)->update(['reservation_expired_at' => $expiredAt]);
            DB::table('lease_contracts')->where('id', $contractId)->update(['status' => 'terminated']);
            DB::table('beds')->where('id', $bedId)->update(['status' => 'vacant']);
            DB::table('tenants')->where('id', $tenantId)->update(['status' => 'inactive']);
            $userId = DB::table('tenants')->where('id', $tenantId)->value('user_id');
            if ($userId) {
                DB::table('users')->where('id', $userId)->update(['is_active' => false]);
            }
        }
    }

    /** One calendar-month rent bill, due on the 1st (or $due for overdue demos). */
    private function monthlyBill(int $contractId, int $tenantId, float $rate, Carbon $month, Carbon $due, ?Carbon $issuedAt = null): int
    {
        return DB::table('billing_statements')->insertGetId([
            'contract_id' => $contractId,
            'tenant_id' => $tenantId,
            'type' => 'monthly',
            'billing_period_start' => $month->copy()->startOfMonth()->toDateString(),
            'billing_period_end' => $month->copy()->endOfMonth()->toDateString(),
            'due_date' => $due->toDateString(),
            'base_rent' => $rate,
            // Water, electricity and Wi-Fi are included in Pureza's rent.
            'utilities_amount' => 0,
            'wifi_amount' => 0,
            'penalty_amount' => 0,
            'total_amount' => $rate,
            'status' => 'unpaid',
            'created_at' => ($issuedAt ?? $month->copy()->startOfMonth())->copy()->setTime(0, 5),
            'updated_at' => now(),
        ]);
    }

    /**
     * Pays a bill in full. on_time: within the grace period (1st-4th).
     * late: after it, with Pureza's one-time 10% late fee added first --
     * the same penalty BillingStatement::applyLatePenalties() creates.
     */
    private function settle(int $billId, int $tenantId, float $rate, Carbon $due, string $style, int $seed, ?string $note = null): void
    {
        $graceEnd = $due->copy()->addDays($this->grace);
        $method = ['cash', 'gcash', 'bank_transfer', 'gcash'][$seed % 4];

        if ($style === 'late') {
            $this->penalty($tenantId, $billId, 'late_payment', $this->lateFeeDescription($rate, $due), round($rate * 0.10, 2),
                $graceEnd->copy()->addDay(), 'active', null, null);
            $total = $this->refreshBillTotal($billId);
            $paidOn = $graceEnd->copy()->addDays(2 + $seed % 6);
        } else {
            $total = $rate;
            $paidOn = $due->copy()->addDays($seed % ($this->grace + 1));
        }
        if ($paidOn->gt($this->today)) {
            $paidOn = $this->today->copy();
        }

        $this->payment($billId, $tenantId, $total, $method, $paidOn, 'approved', $note);
        DB::table('billing_statements')->where('id', $billId)->update(['status' => 'paid']);
    }

    private function lateFeeDescription(float $rate, Carbon $due): string
    {
        return 'Late payment penalty: 10% of ₱' . number_format($rate, 2) . ' unpaid rent for ' . $due->format('F Y');
    }

    private function depositRefund(int $tenantId, float $rate, Carbon $moveOut, int $n): void
    {
        $deduction = $n % 4 === 0 ? 50.0 : 0.0;
        $refunded = $moveOut->copy()->addDays(5 + $n % 14); // within Pureza's 21 days
        if ($refunded->gt($this->today)) {
            return; // still within the 21 days -- not refunded yet
        }

        DB::table('deposit_refunds')->insert([
            'tenant_id' => $tenantId,
            'deposit_amount' => $rate,
            'deductions_amount' => $deduction,
            'deductions_note' => $deduction ? 'Lost room key (key duplication fee, Payments and Fees Schedule).' : null,
            'refund_amount' => $rate - $deduction,
            'refund_method' => $n % 2 ? 'GCash' : 'Bank transfer',
            'reference_number' => $this->refNo($n % 2 ? 'gcash' : 'bank_transfer'),
            'refunded_at' => $refunded->toDateString(),
            'recorded_by' => $this->admins['kristine'],
            'created_at' => $refunded,
            'updated_at' => $refunded,
        ]);
    }

    /** Shuffled, unique [first, last, gender] people not already used. */
    private function namePool(array $firstNames, array $lastNames): array
    {
        $female = ['Althea', 'Bernadette', 'Charmaine', 'Dianne', 'Elaine', 'Faith', 'Gela', 'Hershey', 'Isabelle', 'Jamaica',
            'Krizia', 'Lovely', 'Maureen', 'Nathalie', 'Odessa', 'Pauline', 'Rhea', 'Shaira', 'Trisha', 'Venice',
            'Bianca', 'Danica', 'Francine', 'Hazel', 'Janelle', 'Lianne', 'Nina', 'Pia', 'Queenie', 'Sofia', 'Vanessa',
            'Ysabel', 'Alyssa', 'Clarisse', 'Ella', 'Gwen', 'Irish', 'Kyla', 'Mika', 'Rica'];
        $pool = [];
        for ($i = 0; $i < 400 && count($pool) < 150; $i++) {
            $first = $firstNames[mt_rand(0, count($firstNames) - 1)];
            $last = $lastNames[mt_rand(0, count($lastNames) - 1)];
            $email = $this->emailFor($first, $last);
            if (isset($this->usedEmails[$email]) || isset($pool[$email])) {
                continue;
            }
            $pool[$email] = [$first, $last, in_array($first, $female, true) ? 'female' : 'male'];
        }

        return array_values($pool);
    }

    /* ------------------------------------------------------------------ */
    /*  4d. Monthly expenses -- the dorm's own bills                       */
    /* ------------------------------------------------------------------ */

    /**
     * 12 months of the building's own costs, so Reports can show Net
     * Profit (payments collected minus these). Pureza includes water,
     * electricity and Wi-Fi in the rent, so the dorm pays them: Meralco is
     * its biggest cost and goes up in the hot months (March-May) when the
     * aircons work hardest; December has 13th-month pay.
     */
    private function seedMonthlyExpenses(): void
    {
        mt_srand(4242);
        $oneOffs = [
            2 => [18500, 'Aircon cleaning and freon recharge (all AC rooms)'],
            5 => [6400, 'Plumbing repair, 3F shared CR'],
            8 => [24000, 'Repainting of hallway and lobby'],
            10 => [4500, 'Pest control (whole building)'],
            11 => [9800, 'Replacement of water pump capacitor'],
        ];

        for ($i = 12; $i >= 1; $i--) { // the last 12 finished months; this month is left for the admin to enter
            $month = $this->today->copy()->startOfMonth()->subMonthsNoOverflow($i);
            $hot = in_array($month->month, [3, 4, 5], true);
            // Caretaker 16k, two cleaners 13k each, night guard 15k; plus 13th month in December.
            $salaries = 57000 + ($month->month === 12 ? 57000 : 0);
            [$other, $note] = $oneOffs[$i] ?? [mt_rand(0, 1) ? mt_rand(1500, 3500) : 0, 'Cleaning supplies and toiletries for common areas'];

            DB::table('monthly_expenses')->insert([
                'month' => $month->toDateString(),
                'electricity' => round(($hot ? 128000 : 96000) + mt_rand(-6000, 8000), 2),
                'water' => round(14500 + mt_rand(-1500, 2500), 2),
                'internet' => 6998, // two 3,499 fiber lines
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
    private function applyScenario(string $state, array $spec, int $n, int $tenantId, int $billId, int $bedId, object $room, float $rate, Carbon $due): void
    {
        $proofDate = $this->today->copy()->max($due); // can't pay a bill before it's issued
        if ($proofDate->gt($this->today)) {
            $proofDate = $this->today->copy();
        }

        switch ($state) {
            case 'partial':
                $this->payment($billId, $tenantId, round($rate / 2, -2), 'cash', $proofDate, 'approved',
                    'Partial muna po, babayaran ko yung natitira sa sweldo.');
                DB::table('billing_statements')->where('id', $billId)->update(['status' => 'partial']);
                break;

            case 'proof_pending':
                $this->payment($billId, $tenantId, $rate, 'gcash', $proofDate, 'pending', 'Time of payment: 08:42. Full payment for this month po.');
                break;

            case 'proof_rejected':
                $this->payment($billId, $tenantId, $rate, 'gcash', $proofDate, 'rejected', 'Bayad ko po for this month.',
                    'Screenshot is cropped -- the reference number and amount are not visible. Please upload the full receipt.');
                break;

            case 'unpaid':
                if (! empty($spec['damage'])) {
                    $this->damageWithPenalty($tenantId, $billId, $bedId, $room);
                }
                break;

            case 'overdue':
                // Pureza's one-time 10% late fee, added the day after the grace period.
                $this->penalty($tenantId, $billId, 'late_payment', $this->lateFeeDescription($rate, $due), round($rate * 0.10, 2),
                    $due->copy()->addDays($this->grace + 1), 'active', null, null);
                $this->refreshBillTotal($billId);
                DB::table('billing_statements')->where('id', $billId)->update(['status' => 'overdue']);
                $this->escalationHistory($tenantId, $billId, $due, $spec['days'], ! empty($spec['paused']));
                break;
        }

    }

    /**
     * Writes the escalation log rows the engine WOULD have written by now,
     * so running `escalation:process` only does the next step instead of
     * replaying every SMS at once. Days are counted from the end of the
     * grace period, like EscalationService.
     */
    private function escalationHistory(int $tenantId, int $billId, Carbon $due, int $days, bool $paused): void
    {
        $bill = DB::table('billing_statements')->where('id', $billId)->first();
        $tenant = DB::table('tenants')->where('id', $tenantId)->first();
        $balance = number_format((float) $bill->total_amount, 2);
        $graceEnd = $due->copy()->addDays($this->grace);

        $steps = [
            [0, 1, 'account_flagged', null, 'resolved'],
        ];
        foreach (EscalationService::STAGE_2_DAYS as $d) {
            $urgency = $d >= 7 ? 'URGENT' : 'Reminder';
            $steps[] = [$d, 2, "sms_reminder_day{$d}",
                "{$urgency}: Your account with NEST PH is now {$d} day(s) overdue. Outstanding balance (incl. penalties): PHP {$balance}. Please pay via the tenant portal to avoid further account restrictions.",
                'sent'];
        }
        [$s3, $s4, $s5, $s6] = array_values(EscalationService::STAGE_DAYS);
        $steps[] = [$s3, 3, 'portal_restricted', "PAYMENT REQUIRED: Your rent of PHP {$balance} (due {$due->format('M j, Y')}) is still unpaid. Pay now and upload your proof at " . route('tenant.billing') . '. Your portal is limited to Billing until this is settled; unpaid balances lead to a formal demand letter. - NEST PH', 'sent'];
        // Tenant Agreement 9.3: only with the emergency contact's consent,
        // and only the amount, due date and penalty -- no tenant details.
        $steps[] = $tenant->emergency_billing_reminders
            ? [$s4, 4, 'emergency_contact_notified',
                "NEST PH billing reminder: amount due PHP {$balance}, due " . $due->format('M j, Y') . ', includes penalty PHP '
                    . number_format((float) $bill->penalty_amount, 2) . '. You receive this because you agreed to billing reminders as an emergency contact. To stop, email dormitorypurezastation@gmail.com.',
                'sent']
            : [$s4, 4, 'emergency_contact_notified',
                'Emergency contact has not agreed to billing reminders (Tenant Agreement 9.3), so the tenant was notified instead (SMS: sent, email: sent). '
                    . "Your account with NEST PH is still overdue. Amount due: PHP {$balance}, due " . $due->format('M j, Y') . ', includes penalty PHP '
                    . number_format((float) $bill->penalty_amount, 2) . '. Please pay via the tenant portal to avoid a formal demand letter.',
                'sent'];
        // Like EscalationService: the deadline is the day before blacklisting
        // (Stage 6), and the amount is everything still overdue.
        $owed = (float) DB::table('billing_statements')->where('tenant_id', $tenantId)->where('status', 'overdue')->sum('total_amount');
        $deadline = $this->demandLetterDeadline($graceEnd);
        $steps[] = [$s5, 5, 'demand_letter_generated', null, 'sent'];
        $steps[] = [$s5, 5, 'demand_letter_sms', EscalationService::demandLetterSms($owed, $deadline), 'sent'];
        $steps[] = [$s6, 6, 'delinquent_blacklisted', null, 'resolved'];
        $steps[] = [$s6, 6, 'blacklist_sms', EscalationService::blacklistSms('PHP ' . number_format($owed, 2)), 'sent'];

        foreach ($steps as [$day, $stage, $action, $message, $status]) {
            if ($days < $day) {
                break;
            }
            if ($action === 'demand_letter_generated') {
                $message = $this->demandLetter($tenantId, $billId, $deadline);
            }
            $at = $graceEnd->copy()->addDays($day)->setTime(6, 0);
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

    /** The day before Stage 6 blacklists (see EscalationService::STAGE_DAYS). */
    private function demandLetterDeadline(Carbon $graceEnd): Carbon
    {
        return $graceEnd->copy()->addDays(EscalationService::STAGE_DAYS[6] - 1)->startOfDay();
    }

    /** Generates a real demand letter PDF the same way EscalationService does. */
    private function demandLetter(int $tenantId, int $billId, Carbon $deadline): ?string
    {
        try {
            $tenant = Tenant::find($tenantId);
            $bills = \App\Models\BillingStatement::where('tenant_id', $tenantId)->where('status', 'overdue')->orderBy('billing_period_start')->get();

            $pdf = Pdf::loadView('pdfs.demand-letter', [
                'tenant' => $tenant,
                'bills' => $bills,
                'totalOwed' => (float) $bills->sum('total_amount'),
                'totalPenalties' => (float) $bills->sum('penalty_amount'),
                'history' => $tenant->escalationLogs()->orderBy('created_at')->get(),
                'deadline' => $deadline->format('F j, Y'),
                'blacklistDate' => $deadline->copy()->addDay()->format('F j, Y'),
                'dormName' => DormitoryProfile::current()->dorm_name ?? 'NEST.PH',
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
            'description' => 'Broken locker door hinge (bed-side locker)',
            'cost' => 800,
            'date_incurred' => $date->toDateString(),
            'created_by' => $this->admins['jerome'],
            'created_at' => $date,
            'updated_at' => $date,
        ]);
        $this->penalty($tenantId, $billId, 'damage', 'Damage to dormitory property: broken locker door hinge (repair cost)', 800, $date, 'active', $damageId);
        $this->refreshBillTotal($billId);

        // A waived penalty with its audit trail (key duplication fee, P50).
        $waivedId = $this->penalty($tenantId, null, 'manual', 'Lost or unreturned key (key duplication)', 50, $this->today->copy()->subDays(40), 'waived');
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
        // tenant # => [title, category, description, (unused: priority is auto-scored), status, daysAgo, assignee, replies[]]
        $tickets = [
            1 => ['Aircon not cooling', 'maintenance_repairs', 'The aircon in Room 101 blows air but it is not cold anymore since last night.', 'non_urgent', 'resolved', 20, 'jerome',
                [['admin', 'jerome', 'Noted po. Papupuntahin namin ang technician bukas ng umaga.'], ['tenant', null, 'Salamat po!'], ['admin', 'jerome', 'Nalinis na po ang filter at na-recharge ang freon. Paki-check po kung okay na.']]],
            2 => ['Sparking outlet near Bed 2', 'electrical_issue', 'The outlet beside my bed sparked when I plugged in my charger. I stopped using it.', 'urgent', 'open', 0, null, []],
            5 => ['Low water pressure in CR', 'plumbing_water_emergency', 'Mahina po ang tulo ng tubig sa shower tuwing 6-7 AM.', 'non_urgent', 'in_progress', 4, 'jerome',
                [['admin', 'jerome', 'Chine-check na po ng plumber ang main line. Update po kami mamaya.']]],
            8 => ['Noisy neighbors after quiet hours', 'noise_roommate_concern', 'May maingay po sa kabilang room past 12 midnight, 3 nights na. Quiet hours po ay 10 PM.', 'non_urgent', 'in_progress', 2, null, []],
            11 => ['Request for a bigger study table in the lobby', 'suggestion_feedback', 'Suggestion lang po: sana may mas malaking study table sa lobby for group study.', 'non_urgent', 'closed', 15, 'kristine',
                [['admin', 'kristine', 'Thank you for the suggestion! Hindi po kasya sa space ng lobby sa ngayon, pero isasama namin sa renovation plan next year.']]],
            12 => ['Question about my rejected GCash payment', 'billing_payment_concern', 'Bakit po na-reject yung payment ko? Nagbayad naman po ako.', 'non_urgent', 'in_progress', 0, 'mark',
                [['admin', 'mark', 'Hi Jasmine, naka-crop po kasi yung screenshot kaya hindi makita ang reference number. Paki-upload po ulit yung buong receipt.']]],
            15 => ['Broken door lock', 'security_concern', 'Hindi po nagla-lock nang maayos ang pinto ng Room 201, kailangan pang itulak.', 'urgent', 'resolved', 9, 'jerome',
                [['admin', 'jerome', 'Napalitan na po ang lock. Paki-kuha po ang bagong susi sa front desk.']]],
            18 => ['WiFi keeps disconnecting', 'facilities_amenities', 'The WiFi on the 2nd floor drops every 10-15 minutes, hard to attend online classes.', 'non_urgent', 'open', 1, null, []],
            20 => ['Ceiling leak near the window', 'structural_damage', 'May tumutulo po sa kisame tuwing malakas ang ulan, malapit sa bintana ng Room 204.', 'urgent', 'in_progress', 3, 'jerome',
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
            // Tenants can attach up to 3 photos; two demo tickets have one.
            'attachment_paths' => in_array($n, [2, 20], true) ? json_encode([$this->files['ticket_photo']]) : null,
            // Scored the same way a real submission is.
            ...\App\Models\MaintenanceTicket::autoPriorityFor($cat, $title, $desc),
            'responded_at' => ($status !== 'open' || $replies) ? $created->copy()->addMinutes(20) : null,
            // Demo of an external delay with a revised date.
            'delay_reason' => $n === 20 ? 'Roof gutter repair needs 2 dry days; the roofer is booked until the rain stops.' : null,
            'revised_due_at' => $n === 20 ? now()->addDays(3)->setTime(17, 0) : null,
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
        // The first review shows the "photos on a review" feature, using the
        // review photos from database/seeders/demo-assets (reviews allow up to 3).
        static $photosUsed = false;
        $photos = null;
        if (! $photosUsed) {
            $photos = $this->reviewPhotos ? json_encode($this->reviewPhotos) : null;
            $photosUsed = true;
        }

        $at = $this->today->copy()->subDays(5 + $tenantId % 20);
        DB::table('reviews')->insert([
            'tenant_id' => $tenantId,
            'rating' => $rating,
            'comment' => $comment,
            'photos' => $photos,
            'is_approved' => true,
            'status' => 'published',
            'created_at' => $at,
            'updated_at' => $at,
        ]);
    }

    /**
     * "Report a Tenant" tickets: one tenant reporting another, at each
     * stage (new, being handled, resolved).
     */
    private function seedTenantReports(): void
    {
        $t = fn (string $first, string $last) => DB::table('tenants')->where('email', $this->emailFor($first, $last))->first();

        $reports = [
            // reporter, reported, reason, title, description, status, days ago, assignee, replies
            [['Hannah Grace', 'Soriano'], ['Nicole Joy', 'Ramos'], 'noise_disturbance', 'Roommate playing music past quiet hours',
                'Nagpapatugtog po siya ng malakas na music hanggang 1 AM, kahit quiet hours na po ng 10 PM. Hindi po ako makatulog bago ang exam.', 'open', 0, null, []],
            [['Emmanuel Jose', 'Villareal'], ['Kenneth Bryan', 'Sy'], 'unauthorized_visitors', 'Visitor stayed overnight in Room 303',
                'May bisita po siyang lalaki na nag-overnight sa room kagabi. Bawal po ang overnight visitors (Rules and Regulations).', 'in_progress', 2, 'jerome',
                [['admin', 'jerome', 'Salamat po sa report. Kakausapin namin siya at titingnan ang visitor logbook ng guard.']]],
            [['Angela Marie', 'Villanueva'], ['Bea Katrina', 'Pascual'], 'cleanliness_hygiene', 'Leftover food left in the room',
                'Ilang araw na pong may naiwang pagkain sa table, nagkakaroon na po ng ipis.', 'resolved', 8, 'kristine',
                [['admin', 'kristine', 'Na-remind na po namin si Bea at nalinis na ang area. Paki-report lang po ulit kung maulit.'], ['tenant', null, 'Okay na po, salamat!']]],
        ];

        foreach ($reports as $i => [$reporter, $reported, $reason, $title, $desc, $status, $daysAgo, $assignee, $replies]) {
            $from = $t(...$reporter);
            $about = $t(...$reported);
            if (! $from || ! $about) {
                continue;
            }
            $created = $daysAgo === 0 ? now()->subHours(1) : $this->today->copy()->subDays($daysAgo)->setTime(21, 15 + $i * 10);
            $bedId = DB::table('lease_contracts')->where('tenant_id', $from->id)->latest('id')->value('bed_id');

            $ticketId = DB::table('maintenance_tickets')->insertGetId([
                'tenant_id' => $from->id,
                'bed_id' => $bedId,
                'reported_tenant_id' => $about->id,
                'report_reason' => $reason,
                'title' => $title,
                'category' => 'tenant_report',
                'description' => $desc,
                'attachment_paths' => null,
                ...\App\Models\MaintenanceTicket::autoPriorityFor('tenant_report', $title, $desc, $reason),
                'responded_at' => $replies ? $created->copy()->addMinutes(45) : null,
                'status' => $status,
                'assigned_to' => $assignee ? $this->admins[$assignee] : null,
                'resolved_at' => $status === 'resolved' ? $created->copy()->addDays(1) : null,
                'created_at' => $created,
                'updated_at' => $created->copy()->addHours(count($replies) + 1),
            ]);

            foreach ($replies as $r => [$who, $admin, $message]) {
                DB::table('ticket_replies')->insert([
                    'ticket_id' => $ticketId,
                    'user_id' => $who === 'admin' ? $this->admins[$admin] : null,
                    'tenant_id' => $who === 'tenant' ? $from->id : null,
                    'message' => $message,
                    'created_at' => min($created->copy()->addHours(($r + 1) * 3), now()),
                ]);
            }
        }
    }

    /**
     * The tenant bell (notification panel), filled with what the app itself
     * would have sent each scenario tenant: new bills, payment results,
     * penalties, escalation steps, ticket replies and deposit refunds.
     * Anything older than 3 days is marked as read. Reminders that the app
     * makes on its own (bill due / overdue, lease ending) are left to it.
     */
    private function seedTenantNotifications(): void
    {
        $peso = fn ($n) => '₱' . number_format((float) $n, 2);
        $rows = [];
        $add = function (int $tenantId, string $type, string $title, ?string $body, string $link, $at, ?string $key = null) use (&$rows) {
            $at = Carbon::parse($at);
            $rows[] = [
                'tenant_id' => $tenantId, 'type' => $type, 'title' => $title, 'body' => $body, 'link' => $link, 'dedupe_key' => $key,
                'read_at' => $at->lt($this->today->copy()->subDays(3)) ? $at->copy()->addHours(5) : null,
                'created_at' => $at, 'updated_at' => $at,
            ];
        };

        // Only the hand-made scenario tenants (and the moved-out Gabriel), so the panel stays readable.
        $emails = collect($this->tenantSpecs())->map(fn ($s) => $this->emailFor($s[0], $s[1]));
        $tenants = DB::table('tenants')->whereIn('email', $emails)->get()->keyBy('id');
        $ids = $tenants->keys();

        // New bills: the latest monthly bill of each tenant.
        foreach (DB::table('billing_statements')->whereIn('tenant_id', $ids)->where('type', 'monthly')
            ->whereIn('id', DB::table('billing_statements')->selectRaw('MAX(id)')->where('type', 'monthly')->groupBy('tenant_id'))->get() as $bill) {
            $add($bill->tenant_id, 'bill_new', 'Your bill for ' . Carbon::parse($bill->billing_period_start)->format('F Y') . ' is ready',
                'Total: ' . $peso($bill->base_rent) . ', due on ' . Carbon::parse($bill->due_date)->format('F j, Y') . '.', '/billing', $bill->created_at);
        }

        // Payment results from the last 45 days.
        foreach (DB::table('payments')->whereIn('tenant_id', $ids)->whereIn('status', ['approved', 'rejected'])
            ->where('created_at', '>=', $this->today->copy()->subDays(45))->get() as $p) {
            $at = $p->reviewed_at ?? $p->created_at;
            $p->status === 'approved'
                ? $add($p->tenant_id, 'payment_approved',
                    $p->payment_method === 'cash' ? 'Cash payment of ' . $peso($p->amount_paid) . ' received' : 'Your payment of ' . $peso($p->amount_paid) . ' was approved',
                    'Thank you. Your receipt is ready to download on the Billing page.', '/billing', $at)
                : $add($p->tenant_id, 'payment_rejected', 'Your payment proof of ' . $peso($p->amount_paid) . ' was not accepted',
                    'Reason: ' . $p->review_notes . ' Please submit a new proof of payment.', '/billing', $at);
        }

        // Penalties: automatic late fees on unpaid bills, manual and damage charges, waivers.
        foreach (DB::table('penalties')->whereIn('tenant_id', $ids)->where('created_at', '>=', $this->today->copy()->subDays(45))->get() as $pen) {
            $billPaid = $pen->billing_id && DB::table('billing_statements')->where('id', $pen->billing_id)->value('status') === 'paid';
            if ($pen->type === 'late_payment' && ! $billPaid) {
                $add($pen->tenant_id, 'late_penalty', 'A late payment penalty was added',
                    'Your rent was not paid within the grace period, so a one-time penalty of ' . $peso($pen->amount) . ' was added to the bill.',
                    '/billing', $pen->created_at, "late_penalty:{$pen->billing_id}");
            } elseif ($pen->status === 'waived') {
                $add($pen->tenant_id, 'penalty_waived', 'A penalty of ' . $peso($pen->amount) . ' was waived',
                    $pen->description . '. You no longer need to pay it.', '/billing', Carbon::parse($pen->created_at)->addDay());
            } elseif ($pen->type !== 'late_payment') {
                $add($pen->tenant_id, 'penalty_added',
                    ($pen->type === 'damage' ? 'A damage charge of ' : 'A penalty of ') . $peso($pen->amount) . ' was added',
                    $pen->description . '. It will be included in your next bill.', '/billing', $pen->created_at);
            }
        }

        // Escalation steps the tenant is told about.
        $owed = fn ($tenantId) => $peso(DB::table('billing_statements')->where('tenant_id', $tenantId)->where('status', 'overdue')->sum('total_amount'));
        foreach (DB::table('escalation_logs')->whereIn('tenant_id', $ids)->whereIn('action_type', ['portal_restricted', 'demand_letter_generated', 'delinquent_blacklisted'])->get() as $log) {
            if ($log->action_type === 'delinquent_blacklisted') {
                $add($log->tenant_id, 'blacklisted', 'Your account has been deactivated',
                    $owed($log->tenant_id) . ' remained unpaid after the demand letter deadline, so your account has been blacklisted and online payment is no longer available. Settle the balance directly with the dormitory office.',
                    '/my/delinquency', $log->created_at, "blacklisted:{$log->billing_id}");

                continue;
            }
            $log->action_type === 'portal_restricted'
                ? $add($log->tenant_id, 'account_restricted', 'Payment required: your rent is overdue',
                    'Pay your outstanding balance now and upload your proof on the Billing page. Until it is settled, only Billing and Delinquency are open in your portal, and the next step is a formal demand letter.',
                    '/billing', $log->created_at, "account_restricted:{$log->billing_id}")
                : $add($log->tenant_id, 'demand_letter', 'A formal demand letter has been issued',
                    'Pay ' . $owed($log->tenant_id)
                        . ' by ' . Carbon::parse($log->created_at)->format('F j, Y') . ' to avoid being blacklisted. You can download the letter on the Delinquency page.',
                    '/my/delinquency', $log->created_at, "demand_letter:{$log->billing_id}");
        }

        // Staff replies and resolved tickets.
        foreach (DB::table('ticket_replies')->join('maintenance_tickets', 'maintenance_tickets.id', '=', 'ticket_replies.ticket_id')
            ->whereIn('maintenance_tickets.tenant_id', $ids)->whereNotNull('ticket_replies.user_id')
            ->select('maintenance_tickets.tenant_id', 'maintenance_tickets.title', 'ticket_replies.message', 'ticket_replies.created_at')->get() as $r) {
            $add($r->tenant_id, 'ticket_reply', "New reply on your ticket \"{$r->title}\"", Str::limit($r->message, 140), '/my/tickets', $r->created_at);
        }
        foreach (DB::table('maintenance_tickets')->whereIn('tenant_id', $ids)->where('status', 'resolved')->get() as $tk) {
            $add($tk->tenant_id, 'ticket_resolved', "Your ticket \"{$tk->title}\" is now Resolved", null, '/my/tickets', $tk->resolved_at);
        }
        foreach (DB::table('maintenance_tickets')->whereIn('tenant_id', $ids)->whereNotNull('revised_due_at')->get() as $tk) {
            $add($tk->tenant_id, 'ticket_update', "Your ticket \"{$tk->title}\" is delayed",
                'Expected resolution: ' . Carbon::parse($tk->revised_due_at)->format('M j, Y g:ia') . '. Reason: ' . Str::limit($tk->delay_reason, 120),
                '/my/tickets', $this->today->copy()->subDay()->setTime(16, 0));
        }

        // Deposit refunds after moving out.
        foreach (DB::table('deposit_refunds')->whereIn('tenant_id', $ids)->get() as $d) {
            $add($d->tenant_id, 'deposit_refunded', 'Your security deposit refund of ' . $peso($d->refund_amount) . ' was recorded',
                'Sent via ' . $d->refund_method . ' on ' . Carbon::parse($d->refunded_at)->format('F j, Y') . '.', '/billing', $d->created_at);
        }

        foreach (array_chunk($rows, 200) as $chunk) {
            // insertOrIgnore: the app may already have sent some of these
            // (e.g. a late_penalty notice while bills were built); keep that one.
            DB::table('tenant_notifications')->insertOrIgnore($chunk);
        }
        $this->command?->info('Seeded ' . count($rows) . ' tenant notifications.');
    }

    /* ------------------------------------------------------------------ */
    /*  6. Applications still in the pipeline                              */
    /* ------------------------------------------------------------------ */

    private function seedPendingApplications(): void
    {
        $apps = [
            // first, last, gender, dob, type, school, bed, status, note, days ago, emergency contact agreed to billing reminders
            // "Ana" in the walkthrough script: pending, approved live in Part 2.
            ['Ana Beatriz', 'Salonga', 'female', '2006-03-08', 'student', 'Polytechnic University of the Philippines', '202-2', 'pending', null, 2, true],
            ['Ryan Christopher', 'Santiago', 'male', '2005-09-18', 'student', 'University of Santo Tomas', '203-3', 'pending', null, 1, true],
            ['Alyssa Mae', 'Mercado', 'female', '2006-04-03', 'student', 'Far Eastern University', '301-3', 'pending', null, 0, false],
            // Waiting 5 days: past the 3-day limit, so it shows as overdue on the admin bell (admins already emailed).
            ['Kevin James', 'Dizon', 'male', '2000-10-10', 'full_time_employee', 'Concentrix Philippines', '302-2', 'pending', null, 5, true],
            ['Sophia Isabel', 'Lopez', 'female', '2004-05-25', 'student', 'San Beda University', '303-2', 'rejected',
                'Requested stay is only 1 month; the dormitory requires a minimum 3-month stay (Payments and Fees Schedule 4.1). A short-term stay needs a separate agreement -- please contact the office.', 6, false],
            ['Daniel Lorenzo', 'Cruz', 'male', '2005-01-15', 'student', 'Mapúa University', '303-3', 're_application_requested',
                'Uploaded ID is blurry and the name cannot be read. Please re-apply with a clear photo of a valid school or government ID.', 4, true],
            ['Mika Ella', 'Santos', 'female', '2006-08-12', 'student', 'Centro Escolar University', '303-5', 'cancelled', null, 9, false],
        ];

        foreach ($apps as $i => [$first, $last, $gender, $dob, $type, $school, $bedKey, $status, $note, $daysAgo, $consent]) {
            $created = $this->today->copy()->subDays($daysAgo)->setTime(13 + $i, 5);
            $start = $this->today->copy()->addDays(3 + $i * 3);
            $end = $status === 'rejected'
                ? $start->copy()->addMonthNoOverflow()->endOfMonth()->startOfDay()
                : DormitoryProfile::current()->minimumEndDate($start)->addMonthsNoOverflow($i % 3)->endOfMonth()->startOfDay();
            $home = ['Brgy. Poblacion, Bontoc, Mountain Province', 'Brgy. Bagumbayan, Taguig City', 'Brgy. Malinta, Valenzuela City', 'Brgy. San Isidro, Cainta, Rizal',
                'Brgy. Poblacion, Muntinlupa City', 'Brgy. Tambo, Parañaque City', 'Brgy. Sto. Niño, Marikina City'][$i];
            $row = [
                'first_name' => $first,
                'last_name' => $last,
                'contact_number' => $first === 'Ana Beatriz' ? self::ANA_SMS_NUMBER : $this->phone(200 + $i),
                'email' => $this->emailFor($first, $last),
                'home_address' => $home,
                'emergency_contact_name' => ['Ramon', 'Rommel', 'Liza', 'Jun', 'Maricel', 'Bong', 'Tess'][$i] . " {$last}",
                'emergency_contact_number' => $this->phone(300 + $i),
                'emergency_contact_relation' => $i % 2 ? 'Mother' : 'Father',
                'bed_id' => $this->beds[$bedKey],
                'preferred_start_date' => $start->toDateString(),
                'tenant_end_date' => $end->toDateString(),
            ];

            $packet = $this->signedPacket($row + ['emergency_billing_consent' => $consent], $row['bed_id'], $created, $row['emergency_contact_name']);

            DB::table('applications')->insert($row + [
                'birthdate' => $dob,
                'gender' => $gender,
                'nationality' => 'Filipino',
                'medical_condition' => 'None',
                'occupation' => $type === 'student' ? 'Student' : 'Employee',
                'school_company' => $school,
                'school_company_address' => 'Manila',
                'type_of_tenant' => $type,
                'id_document_path' => $first === 'Ana Beatriz' ? $this->files['ana_id'] : $this->files['id'][0],
                'emergency_contact_id_path' => $first === 'Ana Beatriz' ? $this->files['ana_emergency_id'] : $this->files['emergency_id'],
                'signed_contract_path' => $packet,
                'emergency_contact_signed' => true,
                'emergency_billing_consent' => $consent,
                'dpa_consent' => true,
                'status' => $status,
                // applications:notify-overdue already emailed the admins about it.
                'overdue_notified_at' => $status === 'pending' && $daysAgo >= 3 ? $created->copy()->addDays(3)->setTime(7, 0) : null,
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
            ['Aira Nicole Dimaculangan', 'Hello po! May available pa po bang bedspace for female this November? Magkano po ang 6-person AC room?', 'Room with AC, 6 persons', '301', 'new', null, 0],
            ['Rhenz Adrian Pacheco', 'Good day! Pwede po ba mag-ocular visit this Saturday? Working po ako sa Makati, looking for a solo room.', 'Solo fan room', '105', 'new', null, 1],
            ['Janella Marie Quiambao', 'Kasama na po ba ang WiFi at kuryente sa monthly rate?', 'Room with AC, 4 persons', null, 'contacted',
                'Hi Janella! Opo, kasama na po ang tubig, kuryente at WiFi sa monthly rate. ₱4,200 per bed po ang 4-person AC room sa 2nd to 5th floor. Welcome po kayong mag-visit!', 3],
            ['Luis Gabriel Soriano', 'Is there a curfew? I have night classes until 9 PM.', 'Room with AC, 10–16 persons', '303', 'contacted',
                'Hello Luis! Curfew is 11 PM to 4 AM, so 9 PM classes are no problem. If you ever need to come home later, just inform the office in advance. Feel free to apply online through our website.', 5],
            ['Ryan Christopher Santiago', 'Interested po ako sa Room 203, pwede po ba mag-apply online?', 'Room with AC, 4 persons', '203', 'converted',
                'Yes po! Na-send na namin ang link. Paki-fill up lang po ang application form at pirmahan ang documents online.', 7],
            ['Mylene Castillo', 'Pwede po ba ang pets? May maliit po akong pusa.', null, null, 'closed',
                'Sorry po, bawal po ang pets sa dormitory (Rules and Regulations, item 11). Salamat sa interest!', 12],
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
            [$this->admins['kristine'], "Reminder: Rent is due on the 1st of every month. You have a 3-day grace period; after that, a one-time 10% late fee is added. Pay in cash at the admin office, or through our official GCash (0917 893 2970, Patricia Joy N.) or BDO account, then upload your proof of payment in the portal.", false, 6,
                [[null, $t('Christian Dave', 'Torres'), 'Pwede po ba partial muna ngayong week?'],
                 [$this->admins['mark'], null, 'Pwede po, pero may 10% late fee sa natitirang upa kung lumampas sa 3-day grace period.']]],
            [$this->ownerId, "Fire drill and building inspection next Wednesday, 3:00 PM. Attendance is required for all tenants who are in the building. Please review the evacuation plan posted on every floor (Rules and Regulations, item 12). Comments are turned off for this post.", true, 10, []],
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

    /** Ana's bed (Room 202: 4-person AC, 2nd floor, P4,200 per bed); move-in = advance + deposit. */
    private function anaMoveInFee(): float
    {
        return 4200 * 2;
    }

    /**
     * Walkthrough Part 1: Rooms 101 and 102 have the VR tours (photos from
     * seedDemoPhotos). The public VR page only lists rooms with a vacant
     * bed, so 101-4 and 102-4 are left empty (LEAVE_VACANT). Room 101 has no
     * arrows between its scenes so Vince can add one live in step 1.4;
     * Room 102's two scenes are already linked.
     */
    private function prepareVrDemo(): void
    {
        $tourRooms = [$this->rooms['101']->id, $this->rooms['102']->id];

        DB::table('rooms')->whereNotIn('id', $tourRooms)->update(['vr_visibility' => 'draft']);
        DB::table('rooms')->whereIn('id', $tourRooms)->update(['vr_visibility' => 'public']);
    }

    /**
     * Copies the photos in database/seeders/demo-assets into storage and puts
     * each one in its place, so every reset (locally or on Laravel Cloud)
     * looks the same. To change a demo photo, replace the file in that
     * folder (keep the same name) and run: php artisan demo:reset --force
     */
    private function seedDemoPhotos(): void
    {
        $disk = Storage::disk('public');
        $copy = function (string $file, string $to) use ($disk): string {
            $disk->put($to, File::get(database_path("seeders/demo-assets/{$file}")));

            return $to;
        };

        // VR tours: [room, scene title, file, stored as]. A room's first scene is where its tour opens.
        DB::table('vr_hotspots')->delete();
        foreach (VrScene::all() as $old) {
            $disk->delete(array_filter([$old->panorama_path, $old->filled_path]));
        }
        DB::table('vr_scenes')->delete();

        $scenes = [
            ['101', 'Living Room', 'Room 101 Starting point Living Room.jpg', 'vr-scenes/demo/room-101-living-room.jpg'],
            ['101', 'Kitchen', 'Room 101 Starting Point Kitchen.jpg', 'vr-scenes/demo/room-101-kitchen.jpg'],
            ['102', 'Living Room', 'Room 102 Living Room.jpg', 'vr-scenes/demo/room-102-living-room.jpg'],
            ['102', 'Bedroom', 'Room 102 Bedroom.jpg', 'vr-scenes/demo/room-102-bedroom.jpg'],
        ];
        $filler = app(PanoramaFillService::class);
        $made = ['101' => [], '102' => []];
        foreach ($scenes as [$no, $title, $file, $to]) {
            $scene = VrScene::create([
                'room_id' => $this->rooms[$no]->id,
                'title' => $title,
                'panorama_path' => $copy($file, $to),
                'photo_updated_at' => now(),
                'is_default' => $made[$no] === [],
                'sort_order' => count($made[$no]),
                // All four are full 360° panoramas (2:1 photos).
                'haov' => 360, 'vaov' => 180, 'v_offset' => 0, 'is_partial' => false,
            ]);
            $filler->fill($scene); // a smaller copy for phones, like a normal upload
            $made[$no][] = $scene;
        }

        // Room 102: arrows both ways between the Living Room and the Bedroom.
        [$living, $bedroom] = $made['102'];
        DB::table('vr_hotspots')->insert([
            ['vr_scene_id' => $living->id, 'target_scene_id' => $bedroom->id, 'pitch' => -5, 'yaw' => 0, 'label' => 'Go to Bedroom', 'created_at' => now(), 'updated_at' => now()],
            ['vr_scene_id' => $bedroom->id, 'target_scene_id' => $living->id, 'pitch' => -5, 'yaw' => 180, 'label' => 'Go to Living Room', 'created_at' => now(), 'updated_at' => now()],
        ]);
        DB::table('rooms')->where('id', $this->rooms['101']->id)->update(['vr_caption' => '4-person room with its own living room and kitchen']);
        DB::table('rooms')->where('id', $this->rooms['102']->id)->update(['vr_caption' => 'Quiet 4-person room with a living room']);

        // Room listing photos: one for the 4-person rooms, one for the 10–16 person rooms.
        DB::table('room_photos')->delete();
        $fourPax = $copy('4 pax room placeholder image 1.webp', 'room-photos/demo/4-pax-room.webp');
        $bigRoom = $copy('placeholder image 8 pax room 2.webp', 'room-photos/demo/big-room.webp');
        // Rooms with real VR photos get no placeholder, so the Rooms page shows their tour photo instead.
        $hasVr = DB::table('vr_scenes')->distinct()->pluck('room_id')->all();
        foreach ($this->rooms as $room) {
            if (in_array($room->id, $hasVr)) {
                continue;
            }
            $path = match ($room->type) {
                'Room with AC, 4 persons' => $fourPax,
                'Room with AC, 10–16 persons' => $bigRoom,
                default => null,
            };
            if ($path) {
                DB::table('room_photos')->insert(['room_id' => $room->id, 'path' => $path, 'sort_order' => 0, 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        // Photos on the first published review (see review()).
        $this->reviewPhotos = [
            $copy('Review photo.jpg', 'reviews/demo/review-photo-1.jpg'),
            $copy('review photo 2.jpg', 'reviews/demo/review-photo-2.jpg'),
        ];

        // Homepage slideshow (shown after the cover photo).
        DormitoryProfile::current()->update(['hero_photo_paths' => [
            $copy('Slideshow landing page 1.webp', 'dormitory-profile/hero/demo-slide-1.webp'),
            $copy('Slideshow landing page 2.webp', 'dormitory-profile/hero/demo-slide-2.webp'),
        ]]);

        // Pureza's GCash QR.
        DB::table('payment_methods')->whereRaw('LOWER(name) = ?', ['gcash'])
            ->update(['qr_path' => $copy('DORM gcash qr.jpg', 'payment-qr/demo-gcash.jpg')]);

        // Dummy BIR Certificate of Registration for the Legitimacy Documents section.
        DormitoryProfile::current()->update(['bir_registration_path' => $copy('BIR.png', 'dormitory-profile/demo-bir-registration.png')]);
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
            'payment_method_label' => ['cash' => 'Cash Payment', 'gcash' => 'GCash', 'bank_transfer' => 'BDO'][$method],
            'reference_number' => $online ? $this->refNo($method) : null,
            'payment_date' => $date->toDateString(),
            'status' => $status,
            'proof_path' => $online ? ($this->files['proof'] ?? null) : null,
            'notes' => $notes,
            'review_notes' => $review,
            'reviewed_by' => $online && $reviewed ? $this->admins['mark'] : null,
            'reviewed_at' => $online && $reviewed ? min($date->copy()->addDay()->setTime(10, 0), now()) : null,
            'recorded_by' => $online ? null : $this->admins['mark'],
            'created_at' => $date->copy()->setTime(9 + $billId % 9, 15),
        ]);
    }

    private function penalty(int $tenantId, ?int $billId, string $type, string $desc, float $amount, Carbon $date, string $status, ?int $damageId = null, ?string $createdByKey = 'kristine'): int
    {
        // Late fees are added automatically by the system (no admin).
        $createdBy = $createdByKey ? $this->admins[$createdByKey] : null;

        $id = DB::table('penalties')->insertGetId([
            'tenant_id' => $tenantId,
            'damage_id' => $damageId,
            'billing_id' => $billId,
            'type' => $type,
            'description' => $desc,
            'amount' => $amount,
            'date_incurred' => $date->toDateString(),
            'status' => $status,
            'created_by' => $createdBy,
            'created_at' => $date,
            'updated_at' => $date,
        ]);
        DB::table('penalty_audit_logs')->insert([
            'penalty_id' => $id,
            'action' => 'created',
            'performed_by' => $createdBy,
            'reason' => $createdBy ? null : 'Added automatically after the grace period ended.',
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
        $slug = fn ($s) => preg_replace('/[^a-z]/', '', strtolower(Str::ascii($s)));
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
     * The same signed copy of Pureza's three documents an applicant gets
     * from the real e-sign step (ApplicationController::signContract): the
     * tenant's signature on all three, the emergency contact's signature
     * and their yes/no on billing reminders.
     */
    private function signedPacket(array $applicant, int $bedId, Carbon $signedAt, string $emergencyName): string
    {
        $documents = app(TenancyDocuments::class);
        $name = "{$applicant['first_name']} {$applicant['last_name']}";

        $pdf = $documents->pdf($documents->data($applicant, Bed::find($bedId), [
            'tenant' => 'data:image/png;base64,' . base64_encode($this->demoSignature($name)),
            'emergency_contact' => 'data:image/png;base64,' . base64_encode($this->demoSignature($emergencyName)),
            'signed_at' => $signedAt,
        ]));

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

        // Its own random generator, so the shared one (used for the
        // repeatable tenant history) isn't disturbed.
        $seed = crc32($name);
        $rand = function (int $min, int $max) use (&$seed) {
            $seed = ($seed * 1103515245 + 12345) & 0x7fffffff;

            return $min + $seed % ($max - $min + 1);
        };
        $f1 = $rand(8, 14) / 100;
        $f2 = $rand(20, 35) / 100;
        $amp = $rand(18, 28);

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
     * "View proof" links open something safe to show the panel. Former
     * tenants' contracts point at the dorm's old contract template (they
     * moved out before Pureza's current documents took effect).
     */
    private function pickExistingFiles(): void
    {
        $disk = Storage::disk('public');
        $disk->put('demo/sample-valid-id.png', $this->demoImage('SAMPLE VALID ID', ['Name: (demo tenant)', 'ID No.: 0000-0000-0000', 'For defense demo only']));
        $disk->put('demo/sample-emergency-contact-id.png', $this->demoImage("SAMPLE EMERGENCY CONTACT'S ID", ['Name: (demo emergency contact)', 'ID No.: 0000-0000-0000', 'For defense demo only']));
        // Ana's application uses real ID photos (hers and her emergency contact's),
        // so the panel sees what actual uploads look like.
        $disk->put('application-documents/demo/ana-valid-id.jpg', File::get(database_path('seeders/demo-assets/ana valid ID.jpg')));
        $disk->put('application-documents/demo/ana-emergency-contact-id.png', File::get(database_path('seeders/demo-assets/Ana emergency contact valid ID.png')));
        $disk->put('demo/sample-payment-proof.png', $this->demoImage('SAMPLE PAYMENT PROOF', ['Paid to: Pureza Station Dormitory', 'GCash 0917 893 2970 (Patricia Joy N.)', 'Reference No.: see payment record', 'For defense demo only']));

        $disk->put('demo/sample-ticket-photo.png', $this->demoImage('SAMPLE TICKET PHOTO', ['Photo attached by the tenant', '(e.g. the damaged outlet or ceiling leak)', 'For defense demo only']));

        // The file Ana uploads live in Part 2 of the walkthrough.
        $anaAmount = number_format($this->anaMoveInFee(), 2);
        // A local helper file only; skipped where the app folder is read-only (e.g. a Cloud deploy).
        try {
            File::ensureDirectoryExists(base_path('docs/demo'));
            File::put(base_path('docs/demo/Ana_move-in_payment_proof.png'), $this->demoImage('PAYMENT PROOF - ANA', [
                'Paid to: Pureza Station Dormitory', "Amount: PHP {$anaAmount}", 'Reference No.: 1029 384 756', 'Move-in fee (1 month advance + 1 month deposit)', 'For defense demo only',
            ]));
        } catch (\Throwable) {
        }

        $this->files = [
            'id' => ['demo/sample-valid-id.png'],
            'emergency_id' => 'demo/sample-emergency-contact-id.png',
            'ana_id' => 'application-documents/demo/ana-valid-id.jpg',
            'ana_emergency_id' => 'application-documents/demo/ana-emergency-contact-id.png',
            'contract' => $disk->exists('contracts/dormitory-contract.pdf') ? 'contracts/dormitory-contract.pdf' : null,
            'proof' => 'demo/sample-payment-proof.png',
            'ticket_photo' => 'demo/sample-ticket-photo.png',
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

        // Bundled font (Arimo) so this also works off Windows.
        $font = resource_path('fonts/Arimo-Bold.ttf');
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
