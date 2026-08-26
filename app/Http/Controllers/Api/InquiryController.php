<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InquiryController extends Controller
{
    private const STATUSES = ['new', 'contacted', 'converted', 'closed'];

    /**
     * Public inquiry submission. No authentication required — prospective
     * tenants submit this from the public site.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'room_id' => ['nullable', 'integer', 'exists:rooms,id'],
            'message' => ['nullable', 'string'],
            'preferred_room_type' => ['nullable', 'string', 'max:50'],

            // RA 10173 (Data Privacy Act) consent.
            // 'accepted' means it must be present AND truthy — a missing or
            // false value fails validation. This is the server-side check, so
            // consent can't be bypassed by disabling the frontend checkbox.
            'dpa_consent' => ['required', 'accepted'],
        ], [
            'dpa_consent.required' => 'You must consent to the data privacy notice before submitting.',
            'dpa_consent.accepted' => 'You must consent to the data privacy notice before submitting.',
        ]);

        // At least one way to contact them back, otherwise the inquiry is useless.
        if (empty($data['contact_number']) && empty($data['email'])) {
            return response()->json([
                'message' => 'Please provide either a contact number or an email address.',
                'errors' => [
                    'contact_number' => ['Provide a contact number or an email address.'],
                ],
            ], 422);
        }

        $inquiry = Inquiry::create([
            'full_name' => $data['full_name'],
            'contact_number' => $data['contact_number'] ?? null,
            'email' => $data['email'] ?? null,
            'room_id' => $data['room_id'] ?? null,
            'message' => $data['message'] ?? null,
            'preferred_room_type' => $data['preferred_room_type'] ?? null,
            'dpa_consent' => true,
            'status' => 'new',
        ]);

        // TODO (Week 4): notify admin of new inquiry (email/SMS stub).

        return response()->json([
            'message' => 'Inquiry submitted successfully. We will get back to you shortly.',
            'inquiry' => $inquiry,
        ], 201);
    }

    /**
     * Admin: list inquiries, newest first. Optional ?status= filter.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status' => ['nullable', Rule::in(self::STATUSES)],
        ]);

        $query = Inquiry::with('room:id,room_no,room_type,monthly_rate')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return response()->json($query->get());
    }

    /**
     * Admin: view a single inquiry with its related room and applications.
     */
    public function show(Inquiry $inquiry): JsonResponse
    {
        return response()->json(
            $inquiry->load('room:id,room_no,room_type,monthly_rate', 'applications')
        );
    }

    /**
     * Admin: move an inquiry along its lifecycle
     * (new -> contacted -> converted / closed).
     */
    public function updateStatus(Request $request, Inquiry $inquiry): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
        ]);

        $inquiry->update(['status' => $data['status']]);

        return response()->json([
            'message' => 'Inquiry status updated.',
            'inquiry' => $inquiry->fresh(),
        ]);
    }
}
