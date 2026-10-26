<?php

namespace App\Http\Controllers;

use App\Mail\PartnerRequestReplyMail;
use App\Models\DormitoryProfile;
use App\Models\PartnerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
            'requests' => PartnerRequest::with('teamNotes.author')->when($status, fn ($q) => $q->where('status', $status))->latest()->paginate(15)->withQueryString(),
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
            'reply_message' => ['nullable', 'string', 'max:2000'],
        ]);

        $reply = trim((string) ($data['reply_message'] ?? ''));
        $note = trim((string) ($data['notes'] ?? ''));
        unset($data['reply_message'], $data['notes']);
        $partnerRequest->update($data);

        // Each save with text adds a new note to the history.
        if ($note !== '') {
            $partnerRequest->teamNotes()->create(['user_id' => $request->user()?->id, 'body' => $note]);
        }

        if ($reply === '') {
            return back()->with('status', "Updated {$partnerRequest->dorm_name}.");
        }

        // Team notes stay internal; only the reply goes to the owner.
        $partnerRequest->update([
            'reply_message' => $reply,
            'replied_at' => now(),
            'status' => $partnerRequest->status === 'new' ? 'contacted' : $partnerRequest->status,
        ]);

        try {
            Mail::to($partnerRequest->email)->send(new PartnerRequestReplyMail($partnerRequest->fresh()));
        } catch (\Throwable $e) {
            Log::warning('Partner request reply email failed to send.', [
                'partner_request_id' => $partnerRequest->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('status', "Saved, but the email to {$partnerRequest->email} could not be sent. Check the mail settings.");
        }

        return back()->with('status', "Reply emailed to {$partnerRequest->email}.");
    }
}
