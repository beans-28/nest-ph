<?php

namespace App\Http\Controllers;

use App\Models\DormitoryProfile;
use App\Models\PartnerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * The generic NEST.PH platform pages (before a specific dormitory is chosen).
 */
class PlatformController extends Controller
{
    public function landing()
    {
        $profile = DormitoryProfile::current();

        return view('platform', [
            'dormName' => $profile->dorm_name ?: 'Pureza Station Dormitory',
            'dormAddress' => $profile->address,
            'dormLogoUrl' => $profile->logo_path ? Storage::disk('public')->url($profile->logo_path) : null,
            'dormPhotoUrl' => $profile->heroPhotoUrls()[0] ?? null,
            'dormDescription' => $profile->description,
        ]);
    }

    public function storePartnerRequest(Request $request)
    {
        $data = $request->validate([
            'owner_name' => ['required', 'string', 'max:255'],
            'dorm_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'contact_number' => ['required', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:255'],
            'bed_count' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'message' => ['nullable', 'string', 'max:2000'],
            'dpa_consent' => ['accepted'],
        ]);

        PartnerRequest::create($data + ['status' => 'new']);

        return response()->json(['message' => 'Thank you! The NEST.PH team will contact you within 2–3 business days.']);
    }

    /** NEST.PH team dashboard: every dormitory owner request, newest first. */
    public function dashboard(Request $request)
    {
        $status = $request->query('status');
        if (! array_key_exists((string) $status, PartnerRequest::STATUSES)) {
            $status = null;
        }

        $counts = PartnerRequest::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('platformadmin', [
            'requests' => PartnerRequest::when($status, fn ($q) => $q->where('status', $status))->latest()->paginate(15)->withQueryString(),
            'counts' => $counts,
            'total' => $counts->sum(),
            'activeStatus' => $status,
            'statuses' => PartnerRequest::STATUSES,
        ]);
    }

    public function updatePartnerRequest(Request $request, PartnerRequest $partnerRequest)
    {
        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(PartnerRequest::STATUSES))],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $partnerRequest->update($data);

        return back()->with('status', "Updated {$partnerRequest->dorm_name}.");
    }
}
