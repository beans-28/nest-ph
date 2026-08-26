<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Damage;
use App\Models\Penalty;
use App\Models\PenaltyAuditLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DamageController extends Controller
{
    /**
     * Admin: list recorded damages, newest first. Optional ?tenant_id= filter.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'tenant_id' => ['nullable', 'integer', 'exists:tenants,id'],
        ]);

        $query = Damage::with([
            'tenant:id,full_name',
            'room:id,room_no',
            'bed:id,bed_label',
            'penalty:id,damage_id,amount,status,billing_id',
            'createdBy:id,name',
        ])->latest();

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->input('tenant_id'));
        }

        return response()->json(
            $query->get()->map(fn ($damage) => $this->withPhotoUrl($damage))
        );
    }

    /**
     * Admin: view a single damage record with its linked penalty.
     */
    public function show(Damage $damage): JsonResponse
    {
        $damage->load([
            'tenant',
            'room:id,room_no,room_type',
            'bed:id,bed_label,status',
            'penalty.auditLogs.performedBy:id,name',
            'createdBy:id,name',
        ]);

        return response()->json($this->withPhotoUrl($damage));
    }

    /**
     * Admin: record a damage. This automatically creates a linked penalty for
     * the same amount — the damage is the incident record, the penalty is the
     * billable line item. They're created together in one transaction so a
     * damage can never exist without its charge (or vice versa).
     *
     * Accepts multipart/form-data when a photo is included.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'room_id' => ['nullable', 'integer', 'exists:rooms,id'],
            'bed_id' => ['nullable', 'integer', 'exists:beds,id'],
            'description' => ['required', 'string', 'max:255'],
            'cost' => ['required', 'numeric', 'min:0.01'],
            'date_incurred' => ['required', 'date', 'before_or_equal:today'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:10240'], // 10MB
        ], [
            'date_incurred.before_or_equal' => 'The damage date cannot be in the future.',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('damage-photos', 'public');
        }

        $result = DB::transaction(function () use ($data, $photoPath, $request) {
            $damage = Damage::create([
                'tenant_id' => $data['tenant_id'],
                'room_id' => $data['room_id'] ?? null,
                'bed_id' => $data['bed_id'] ?? null,
                'description' => $data['description'],
                'cost' => $data['cost'],
                'date_incurred' => $data['date_incurred'],
                'photo_path' => $photoPath,
                'created_by' => $request->user()?->id,
            ]);

            $penalty = Penalty::create([
                'tenant_id' => $damage->tenant_id,
                'damage_id' => $damage->id,
                'type' => 'damage',
                'description' => 'Damage: ' . $damage->description,
                'amount' => $damage->cost,
                'status' => 'active',
                'created_by' => $request->user()?->id,
            ]);

            PenaltyAuditLog::create([
                'penalty_id' => $penalty->id,
                'action' => 'created',
                'performed_by' => $request->user()?->id,
                'reason' => 'Auto-created from damage record #' . $damage->id,
                'created_at' => now(),
            ]);

            return ['damage' => $damage, 'penalty' => $penalty];
        });

        return response()->json([
            'message' => 'Damage recorded and penalty created.',
            'damage' => $this->withPhotoUrl($result['damage']->fresh()),
            'penalty' => $result['penalty'],
        ], 201);
    }

    /**
     * Admin: correct a damage record. If the cost changes, the linked penalty
     * is updated to match — but only while that penalty is still active and
     * unbilled, since changing a billed amount would desync the statement.
     */
    public function update(Request $request, Damage $damage): JsonResponse
    {
        $data = $request->validate([
            'description' => ['sometimes', 'required', 'string', 'max:255'],
            'cost' => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'date_incurred' => ['sometimes', 'required', 'date', 'before_or_equal:today'],
            'room_id' => ['nullable', 'integer', 'exists:rooms,id'],
            'bed_id' => ['nullable', 'integer', 'exists:beds,id'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:10240'],
        ]);

        $penalty = $damage->penalty;

        $costChanged = isset($data['cost']) && (float) $data['cost'] !== (float) $damage->cost;

        if ($costChanged && $penalty) {
            if ($penalty->billing_id) {
                return response()->json([
                    'message' => 'This damage\'s penalty is already on a billing statement. Waive that penalty and record a corrected damage instead.',
                ], 409);
            }
            if ($penalty->status !== 'active') {
                return response()->json([
                    'message' => 'This damage\'s penalty has been waived and its amount can no longer be changed.',
                ], 409);
            }
        }

        if ($request->hasFile('photo')) {
            if ($damage->photo_path && Storage::disk('public')->exists($damage->photo_path)) {
                Storage::disk('public')->delete($damage->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('damage-photos', 'public');
        }
        unset($data['photo']);

        DB::transaction(function () use ($damage, $data, $penalty, $costChanged) {
            $damage->update($data);

            if ($penalty && $penalty->status === 'active' && ! $penalty->billing_id) {
                $penalty->update([
                    'description' => 'Damage: ' . $damage->description,
                    'amount' => $damage->cost,
                ]);
            }
        });

        return response()->json([
            'message' => $costChanged
                ? 'Damage updated, and its linked penalty amount updated to match.'
                : 'Damage updated.',
            'damage' => $this->withPhotoUrl($damage->fresh('penalty')),
        ]);
    }

    /**
     * Admin: delete a damage record. Blocked once its penalty has been billed,
     * since removing it would leave the statement's total unexplained.
     */
    public function destroy(Damage $damage): JsonResponse
    {
        $penalty = $damage->penalty;

        if ($penalty && $penalty->billing_id) {
            return response()->json([
                'message' => 'This damage\'s penalty is already on a billing statement and cannot be deleted. Waive the penalty instead.',
            ], 409);
        }

        DB::transaction(function () use ($damage, $penalty) {
            if ($penalty) {
                $penalty->auditLogs()->delete();
                $penalty->delete();
            }

            if ($damage->photo_path && Storage::disk('public')->exists($damage->photo_path)) {
                Storage::disk('public')->delete($damage->photo_path);
            }

            $damage->delete();
        });

        return response()->json(['message' => 'Damage record and its linked penalty deleted.']);
    }

    private function withPhotoUrl(Damage $damage): Damage
    {
        $damage->photo_url = $damage->photo_path
            ? Storage::disk('public')->url($damage->photo_path)
            : null;

        return $damage;
    }
}
