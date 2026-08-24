<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Bed;
use App\Models\Inquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    private const STATUSES = ['pending', 'approved', 'rejected', 'cancelled'];

    /**
     * Public "Apply for Occupancy" submission. No authentication required —
     * the applicant is not a tenant yet, so their personal info is stored on
     * the application itself. tenant_id stays null until approval.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'inquiry_id' => ['nullable', 'integer', 'exists:inquiries,id'],

            // Applicant's own details (no tenants row exists for them yet).
            'full_name' => ['required', 'string', 'max:150'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_number' => ['nullable', 'string', 'max:20'],

            'bed_id' => ['required', 'integer', 'exists:beds,id'],
            'preferred_start_date' => ['nullable', 'date', 'after_or_equal:today'],

            // Same server-side RA 10173 consent enforcement as the inquiry form.
            'dpa_consent' => ['required', 'accepted'],
        ], [
            'dpa_consent.required' => 'You must consent to the data privacy notice before submitting.',
            'dpa_consent.accepted' => 'You must consent to the data privacy notice before submitting.',
            'preferred_start_date.after_or_equal' => 'Preferred start date cannot be in the past.',
        ]);

        if (empty($data['contact_number']) && empty($data['email'])) {
            return response()->json([
                'message' => 'Please provide either a contact number or an email address.',
                'errors' => [
                    'contact_number' => ['Provide a contact number or an email address.'],
                ],
            ], 422);
        }

        $bed = Bed::with('room')->findOrFail($data['bed_id']);

        // Availability is checked live at submission time, not just in the UI.
        if ($bed->status !== 'vacant') {
            return response()->json([
                'message' => 'That bedspace is no longer available. Please choose another.',
            ], 409);
        }

        // Block double-applying for the same bed while one is still pending.
        $alreadyPending = Application::where('bed_id', $bed->id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyPending) {
            return response()->json([
                'message' => 'There is already a pending application for that bedspace.',
            ], 409);
        }

        $application = Application::create([
            'inquiry_id' => $data['inquiry_id'] ?? null,
            'tenant_id' => null, // set on approval, not now
            'full_name' => $data['full_name'],
            'contact_number' => $data['contact_number'] ?? null,
            'email' => $data['email'] ?? null,
            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_number' => $data['emergency_contact_number'] ?? null,
            'bed_id' => $bed->id,
            'preferred_start_date' => $data['preferred_start_date'] ?? null,
            'dpa_consent' => true,
            'status' => 'pending',
        ]);

        // If this came from an inquiry, mark that inquiry as converted.
        if ($application->inquiry_id) {
            Inquiry::where('id', $application->inquiry_id)
                ->where('status', '!=', 'converted')
                ->update(['status' => 'converted']);
        }

        // TODO (Week 4): notify admin of new application (email/SMS stub).

        return response()->json([
            'message' => 'Application submitted successfully. It is now pending review.',
            'application' => $application->load('bed.room:id,room_no,room_type,monthly_rate'),
        ], 201);
    }

    /**
     * Admin: list applications, newest first. Optional ?status= filter.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status' => ['nullable', Rule::in(self::STATUSES)],
        ]);

        $query = Application::with([
            'bed:id,room_id,bed_label,status',
            'bed.room:id,room_no,room_type,monthly_rate',
            'inquiry:id,full_name,status',
            'tenant:id,full_name',
        ])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return response()->json($query->get());
    }

    /**
     * Admin: view a single application in full.
     */
    public function show(Application $application): JsonResponse
    {
        return response()->json($application->load([
            'bed:id,room_id,bed_label,status',
            'bed.room:id,room_no,room_type,monthly_rate',
            'inquiry',
            'tenant',
            'createdBy:id,name',
            'approvedBy:id,name',
            'leaseContract',
        ]));
    }

    /**
     * Applicant-facing status check. Looks up by application id + the contact
     * detail used on the application, so someone without an account can still
     * check their own status without exposing anyone else's.
     */
    public function checkStatus(Request $request): JsonResponse
    {
        $data = $request->validate([
            'application_id' => ['required', 'integer'],
            'contact' => ['required', 'string', 'max:150'],
        ]);

        $application = Application::where('id', $data['application_id'])
            ->where(function ($query) use ($data) {
                $query->where('email', $data['contact'])
                    ->orWhere('contact_number', $data['contact']);
            })
            ->with('bed.room:id,room_no,room_type,monthly_rate')
            ->first();

        if (! $application) {
            return response()->json([
                'message' => 'No application found matching those details.',
            ], 404);
        }

        return response()->json([
            'id' => $application->id,
            'full_name' => $application->full_name,
            'status' => $application->status,
            'preferred_start_date' => $application->preferred_start_date,
            'bed' => $application->bed,
            'submitted_at' => $application->created_at,
        ]);
    }

    /**
     * Admin: cancel a pending application (soft workflow action, not a delete).
     * Approve/reject lives in the Wednesday/Thursday task, since it also
     * creates the tenant + lease contract.
     */
    public function cancel(Application $application): JsonResponse
    {
        if ($application->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending applications can be cancelled.',
            ], 409);
        }

        $application->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Application cancelled.',
            'application' => $application->fresh(),
        ]);
    }
}
