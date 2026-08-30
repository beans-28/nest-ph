<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\InquiryAcknowledgmentMail;
use App\Mail\InquiryReplyMail;
use App\Models\DormitoryProfile;
use App\Models\Inquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class InquiryController extends Controller
{
    private const STATUSES = ['new', 'contacted', 'converted', 'closed'];

    /**
     * Public inquiry submission. No authentication required — prospective
     * tenants submit this from the public site.
     *
     * Matches Use Case Report — Inquiry Form: saves with a timestamp, notifies
     * the admin, sends an acknowledgment email to the visitor, and returns the
     * confirmation message specified in the use case.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'room_id' => ['nullable', 'integer', 'exists:rooms,id'],
            'message' => ['required', 'string', 'max:2000'],
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
            'message' => $data['message'],
            'preferred_room_type' => $data['preferred_room_type'] ?? null,
            'dpa_consent' => true,
            'status' => 'new',
        ]);

        // Use case 3.2: notify the Dormitory Administrator/Owner.
        // Real delivery (email/SMS to a configured admin address) isn't wired
        // up yet — this stub keeps a durable record so nothing silently
        // vanishes in the meantime. Swap for a Mail::to(...) once the team
        // decides where admin notifications should actually go.
        Log::info('[notification stub] inquiry.submitted', [
            'inquiry_id' => $inquiry->id,
            'full_name' => $inquiry->full_name,
            'room_id' => $inquiry->room_id,
        ]);

        // Use case 3.3: acknowledgment email to the visitor. Per the use
        // case's own exception handling, a failed email must not block the
        // submission — it's caught and logged instead of surfacing to the
        // visitor as an error.
        if ($inquiry->email) {
            try {
                Mail::to($inquiry->email)->send(new InquiryAcknowledgmentMail($inquiry));
            } catch (\Throwable $e) {
                Log::warning('Inquiry acknowledgment email failed to send.', [
                    'inquiry_id' => $inquiry->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'message' => 'Your inquiry has been submitted. We will get back to you shortly.',
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
            $inquiry->load('room:id,room_no,room_type,monthly_rate', 'applications', 'repliedBy:id,name')
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

    /**
     * Admin: send a reply. Matches Use Case Report — Inquiry Form steps
     * 6/6.1–6.4: saves the reply against the inquiry, moves status to
     * "contacted" (this schema's equivalent of the use case's "Replied"),
     * emails the reply to the visitor, and confirms.
     */
    public function reply(Request $request, Inquiry $inquiry): JsonResponse
    {
        $data = $request->validate([
            'reply_message' => ['required', 'string', 'max:2000'],
        ]);

        if (! $inquiry->email) {
            return response()->json([
                'message' => 'This inquiry has no email address on file, so a reply cannot be sent.',
            ], 422);
        }

        $inquiry->update([
            'reply_message' => $data['reply_message'],
            'replied_at' => now(),
            'replied_by' => $request->user()?->id,
            'status' => 'contacted',
        ]);

        try {
            Mail::to($inquiry->email)->send(new InquiryReplyMail($inquiry->fresh()));
        } catch (\Throwable $e) {
            Log::warning('Inquiry reply email failed to send.', [
                'inquiry_id' => $inquiry->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'The reply was saved, but the email could not be sent. Please check the mail configuration.',
                'inquiry' => $inquiry->fresh(),
            ], 207);
        }

        return response()->json([
            'message' => 'Reply sent successfully.',
            'inquiry' => $inquiry->fresh(),
        ]);
    }

    /**
     * Renders the admin Inquiry Management page.
     */
    public function page()
    {
        $inquiries = Inquiry::with('room:id,room_no,room_type', 'repliedBy:id,name')
            ->latest()
            ->get()
            ->map(fn ($inquiry) => [
                'id' => $inquiry->id,
                'full_name' => $inquiry->full_name,
                'contact_number' => $inquiry->contact_number,
                'email' => $inquiry->email,
                'message' => $inquiry->message,
                'preferred_room_type' => $inquiry->preferred_room_type,
                'room_no' => $inquiry->room?->room_no,
                'status' => $inquiry->status,
                'reply_message' => $inquiry->reply_message,
                'replied_at' => $inquiry->replied_at?->format('M j, Y g:ia'),
                'replied_by' => $inquiry->repliedBy?->name,
                'created_at' => $inquiry->created_at?->format('M j, Y g:ia'),
            ])->values();

        return view('admininquiries', ['inquiries' => $inquiries]);
    }
}