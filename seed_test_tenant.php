<?php

// ============================================================
// NEST.PH — Test tenant account generator for Delinquency testing
// Bypasses EscalationService/TextbeeService entirely — zero SMS.
// Change $targetStage (0-6) and re-run any time to move the SAME
// test tenant to a different stage.
// ============================================================

$targetStage = 6; // <-- change this (0 through 6) and re-run

$bed = \App\Models\Bed::where('status', 'vacant')->first();
if (! $bed) { throw new Exception('No vacant bed found — free one up in Vacancy Monitoring first.'); }

$tenantRole = \App\Models\Role::firstOrCreate(['role_name' => 'tenant']);

$user = \App\Models\User::updateOrCreate(['email' => 'delinquency.test@nestph.test'], ['name' => 'Delinquency Test Tenant', 'password' => \Illuminate\Support\Facades\Hash::make('password123'), 'role_id' => $tenantRole->id, 'is_active' => true]);

$tenant = \App\Models\Tenant::updateOrCreate(['user_id' => $user->id], ['full_name' => 'Delinquency Test Tenant', 'contact_number' => '09171234567', 'email' => 'delinquency.test@nestph.test', 'emergency_contact_name' => 'Test Emergency Contact', 'emergency_contact_number' => '09179876543', 'status' => 'active', 'is_blacklisted' => false, 'portal_restricted' => false, 'escalation_paused' => false]);

$contract = \App\Models\LeaseContract::where('tenant_id', $tenant->id)->where('status', 'active')->first();

if (! $contract) {
    $contract = \App\Models\LeaseContract::create(['tenant_id' => $tenant->id, 'bed_id' => $bed->id, 'start_date' => now()->subMonths(2)->toDateString(), 'monthly_rate' => $bed->room->monthly_rate ?? 3000, 'esign_status' => 'signed', 'status' => 'active']);
    $bed->update(['status' => 'occupied']);
    $bed->room?->syncStatusFromBeds();
}

\App\Models\EscalationLog::where('tenant_id', $tenant->id)->delete();

$daysOverdueByStage = [0 => 0, 1 => 0, 2 => 7, 3 => 8, 4 => 9, 5 => 10, 6 => 11];
$daysOverdue = $daysOverdueByStage[$targetStage];
$dueDate = now()->subDays($daysOverdue);

$bill = \App\Models\BillingStatement::where('tenant_id', $tenant->id)->where('type', 'monthly')->first();

$baseRent = $contract->monthly_rate;
$penalty = $targetStage >= 1 ? 500 : 0;

if ($bill) {
    $bill->update(['billing_period_start' => $dueDate->copy()->subDays(5)->startOfMonth(), 'billing_period_end' => $dueDate->copy()->subDays(5)->endOfMonth(), 'due_date' => $dueDate, 'base_rent' => $baseRent, 'penalty_amount' => $penalty, 'total_amount' => $baseRent + $penalty, 'status' => $targetStage === 0 ? 'unpaid' : 'overdue']);
} else {
    $bill = \App\Models\BillingStatement::create(['contract_id' => $contract->id, 'tenant_id' => $tenant->id, 'type' => 'monthly', 'billing_period_start' => $dueDate->copy()->subDays(5)->startOfMonth(), 'billing_period_end' => $dueDate->copy()->subDays(5)->endOfMonth(), 'due_date' => $dueDate, 'base_rent' => $baseRent, 'utilities_amount' => 0, 'wifi_amount' => 0, 'penalty_amount' => $penalty, 'total_amount' => $baseRent + $penalty, 'status' => $targetStage === 0 ? 'unpaid' : 'overdue']);
}

$now = now();

$seed = function (int $stage, string $actionType, string $status, ?string $message = null) use ($tenant, $bill, $now) {
    \App\Models\EscalationLog::create(['tenant_id' => $tenant->id, 'billing_id' => $bill->id, 'stage' => $stage, 'action_type' => $actionType, 'message_content' => $message, 'status' => $status, 'created_at' => $now->copy()->subMinutes(6 - $stage), 'updated_at' => $now->copy()->subMinutes(6 - $stage)]);
};

if ($targetStage >= 1) { $seed(1, 'account_flagged', 'resolved'); }
if ($targetStage >= 2) { $seed(2, 'sms_reminder_day1', 'sent', '[TEST SEED — not actually sent] Reminder: overdue balance.'); $seed(2, 'sms_reminder_day3', 'sent', '[TEST SEED — not actually sent] Reminder: overdue balance.'); $seed(2, 'sms_reminder_day7', 'sent', '[TEST SEED — not actually sent] URGENT: overdue balance.'); }
if ($targetStage >= 3) { $seed(3, 'portal_restricted', 'sent', '[TEST SEED — not actually sent] Portal access restricted.'); $tenant->update(['portal_restricted' => true]); }
if ($targetStage >= 4) { $seed(4, 'emergency_contact_notified', 'sent', '[TEST SEED — not actually sent] Emergency contact notified.'); }
if ($targetStage >= 5) { $seed(5, 'demand_letter_generated', 'sent', 'demand-letters/(test-seed-no-real-pdf).pdf'); }
if ($targetStage >= 6) { $seed(6, 'delinquent_blacklisted', 'resolved'); $tenant->update(['is_blacklisted' => true]); }

echo "\n=== Test tenant ready ===\n";
echo "Login:    delinquency.test@nestph.test / password123\n";
echo "Tenant #{$tenant->id}, Bill #{$bill->id}, Stage seeded: {$targetStage}\n";
echo "portal_restricted: " . ($tenant->fresh()->portal_restricted ? 'true' : 'false') . "\n";
echo "is_blacklisted: " . ($tenant->fresh()->is_blacklisted ? 'true' : 'false') . "\n";
echo "No SMS was sent. TextbeeService::send() was never called.\n";