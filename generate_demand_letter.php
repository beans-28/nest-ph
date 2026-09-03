<?php

// ============================================================
// NEST.PH — Generate a REAL Stage 5 demand letter PDF for the test
// tenant, via reflection into EscalationService::stage5DemandLetter().
// Zero SMS: that method never calls TextbeeService::send() -- confirmed
// directly against the source before writing this.
// ============================================================

$tenant = \App\Models\Tenant::where('email', 'delinquency.test@nestph.test')->firstOrFail();

$bill = \App\Models\BillingStatement::where('tenant_id', $tenant->id)
    ->where('status', 'overdue')
    ->latest('due_date')
    ->firstOrFail();

// Clear the seed script's FAKE Stage 5 log row -- otherwise the real
// method sees "already done" and skips without generating anything.
\App\Models\EscalationLog::where('tenant_id', $tenant->id)
    ->where('action_type', 'demand_letter_generated')
    ->delete();

$service = app(\App\Services\EscalationService::class);
$method = new ReflectionMethod($service, 'stage5DemandLetter');
$method->setAccessible(true);
$method->invoke($service, $bill);

$log = \App\Models\EscalationLog::where('tenant_id', $tenant->id)
    ->where('action_type', 'demand_letter_generated')
    ->latest()
    ->first();

echo "\n=== Demand letter generated ===\n";
echo "Log status: " . ($log->status ?? 'MISSING') . "\n";
echo "File path: " . ($log->message_content ?? 'MISSING') . "\n";
echo "File exists on disk: " . (\Illuminate\Support\Facades\Storage::disk('public')->exists($log->message_content ?? '') ? 'YES' : 'NO') . "\n";
echo "No SMS was sent -- stage5DemandLetter() contains no TextbeeService::send() call.\n";