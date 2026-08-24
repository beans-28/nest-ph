// 1. Find a vacant bed to use for testing
$bed = \App\Models\Bed::where('status', 'vacant')->first();
echo "Using bed #{$bed->id} ({$bed->bed_label}), status: {$bed->status}\n";

// 2. Create a test inquiry
$inquiry = \App\Models\Inquiry::create([
    'full_name' => 'Tinker Test',
    'email' => 'tinkertest@example.com',
    'dpa_consent' => true,
    'status' => 'new',
]);
echo "Created inquiry #{$inquiry->id}\n";

// 3. Create a test application against that bed
$application = \App\Models\Application::create([
    'inquiry_id' => $inquiry->id,
    'full_name' => 'Tinker Test',
    'email' => 'tinkertest@example.com',
    'bed_id' => $bed->id,
    'dpa_consent' => true,
    'status' => 'pending',
]);
echo "Created application #{$application->id}, status: {$application->status}\n";

// 4. Simulate approval (same logic as ApplicationController::approve)
$tenant = \App\Models\Tenant::create([
    'full_name' => $application->full_name,
    'email' => $application->email,
]);
$contract = \App\Models\LeaseContract::create([
    'application_id' => $application->id,
    'tenant_id' => $tenant->id,
    'bed_id' => $bed->id,
    'start_date' => now()->toDateString(),
    'monthly_rate' => $bed->room->monthly_rate ?? 3000,
    'esign_status' => 'pending',
    'status' => 'pending',
]);
$bed->update(['status' => 'occupied']);
$application->update(['status' => 'approved', 'tenant_id' => $tenant->id]);

echo "Created tenant #{$tenant->id}\n";
echo "Created contract #{$contract->id}, esign_status: {$contract->esign_status}, status: {$contract->status}\n";
echo "Bed status is now: " . $bed->fresh()->status . "\n";

// 5. Simulate signing (same logic as LeaseContractController::submitSigned)
$contract->update([
    'signed_document_url' => 'signed-contracts/test.pdf',
    'signed_at' => now(),
    'esign_status' => 'signed',
    'status' => 'active',
]);
echo "After signing — esign_status: {$contract->esign_status}, status: {$contract->status}\n";

// 6. Verify relationships resolve correctly end to end
$check = \App\Models\Application::with('tenant', 'bed.room', 'leaseContract')->find($application->id);
echo "Final check — application status: {$check->status}, tenant: {$check->tenant->full_name}, bed: {$check->bed->bed_label}, contract status: {$check->leaseContract->status}\n";

// 7. Cleanup — remove all test data so it doesn't pollute your real DB
$contract->delete();
$application->delete();
$tenant->delete();
$inquiry->delete();
$bed->update(['status' => 'vacant']);
echo "Cleanup done. Bed reset to vacant.\n";
