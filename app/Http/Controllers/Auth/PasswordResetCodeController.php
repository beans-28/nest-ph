<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetCodeMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetCodeController extends Controller
{
    private const CODE_TTL_MINUTES = 10;

    /**
     * Step 1: generate a 5-digit code, store its hash, email it to the user.
     */
    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $code = (string) random_int(10000, 99999);

            DB::table('password_reset_codes')->updateOrInsert(
                ['email' => $user->email],
                ['code' => Hash::make($code), 'created_at' => now()]
            );

            Mail::to($user->email)->send(new PasswordResetCodeMail($code));
        }

        // Always the same response whether or not the email exists, so this
        // endpoint can't be used to check which emails are registered.
        return response()->json([
            'message' => 'If that email is registered, a code has been sent.',
        ]);
    }

    /**
     * Step 2: check the code without consuming it, so the user can still
     * use it again on the final submit.
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:5'],
        ]);

        if (! $this->codeIsValid($request->email, $request->code)) {
            return response()->json([
                'message' => 'That code is invalid or has expired.',
            ], 422);
        }

        return response()->json(['message' => 'Code verified.']);
    }

    /**
     * Step 3: re-verify the code (never trust that step 2 already happened)
     * and actually update the password.
     */
    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:5'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        if (! $this->codeIsValid($request->email, $request->code)) {
            return response()->json([
                'message' => 'That code is invalid or has expired.',
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json(['message' => 'Account not found.'], 422);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_codes')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password updated.']);
    }

    private function codeIsValid(string $email, string $code): bool
    {
        $record = DB::table('password_reset_codes')->where('email', $email)->first();

        if (! $record) {
            return false;
        }

        $expired = Carbon::parse($record->created_at)->diffInMinutes(now()) > self::CODE_TTL_MINUTES;

        return ! $expired && Hash::check($code, $record->code);
    }
}
