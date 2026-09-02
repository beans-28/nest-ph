<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TextbeeService
{
    /**
     * The brand name to use inside SMS body text. Deliberately a space, not
     * a period -- "NEST.PH" matches the domain-name pattern (word.tld) that
     * PH telco anti-smishing filters block outgoing SMS for, since it looks
     * identical to a phishing link like "scam.ph". Confirmed by testing:
     * every message containing "NEST.PH" failed to send, every version
     * without the period succeeded. Use this constant in every SMS template
     * (escalation reminders, eviction notice, etc.) instead of typing the
     * brand name out by hand. The period version ("NEST.PH") is still fine
     * everywhere else -- web UI, emails, PDFs -- since only outgoing SMS
     * goes through the telco filter.
     */
    public const BRAND_NAME = 'NEST PH';

    private string $baseUrl;

    private ?string $apiKey;

    private ?string $deviceId;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.textbee.base_url'), '/');
        $this->apiKey = config('services.textbee.api_key');
        $this->deviceId = config('services.textbee.device_id');
    }

    /**
     * Send an SMS to a single Philippine mobile number via the textbee.dev
     * gateway.
     *
     * Accepts common local formats ("09171234567", "9171234567") and
     * normalizes them to E.164 ("+639171234567") before sending, since
     * that's what the gateway/carriers expect.
     *
     * Returns true on success, false on any failure (bad number, missing
     * config, gateway unreachable, non-2xx response). Never throws --
     * callers (escalation jobs, controllers) should check the boolean and
     * decide what to do next, not have to wrap this in try/catch themselves.
     */
    public function send(string $recipient, string $message): bool
    {
        if (empty($this->apiKey) || empty($this->deviceId)) {
            Log::error('[textbee] Missing TEXTBEE_API_KEY or TEXTBEE_DEVICE_ID in .env — SMS not sent.', [
                'recipient' => $recipient,
            ]);

            return false;
        }

        $normalized = $this->normalizePhilippineNumber($recipient);

        if (! $normalized) {
            Log::warning('[textbee] Recipient number does not look like a valid PH mobile number — SMS not sent.', [
                'recipient' => $recipient,
            ]);

            return false;
        }

        $safeMessage = $this->sanitizeMessage($message);

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
            ])->post("{$this->baseUrl}/gateway/devices/{$this->deviceId}/send-sms", [
                'recipients' => [$normalized],
                'message' => $safeMessage,
            ]);

            if ($response->successful()) {
                Log::info('[textbee] SMS sent.', [
                    'recipient' => $normalized,
                ]);

                return true;
            }

            Log::error('[textbee] SMS send failed — non-2xx response from gateway.', [
                'recipient' => $normalized,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('[textbee] SMS send threw an exception — gateway unreachable or phone offline?', [
                'recipient' => $normalized,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Defense-in-depth: strips the "NEST.PH" domain-like pattern out of any
     * outgoing SMS body, even if a future template accidentally typed it
     * with the period. Case-insensitive, so "nest.ph"/"Nest.Ph" are also
     * caught. Logs a warning when it has to intervene, so the offending
     * template gets noticed and fixed at the source rather than silently
     * patched over every time.
     */
    private function sanitizeMessage(string $message): string
    {
        $fixed = preg_replace('/NEST\.PH/i', self::BRAND_NAME, $message);

        if ($fixed !== $message) {
            Log::warning('[textbee] Message contained "NEST.PH" (blocked by telco filters) — auto-corrected to "NEST PH". Fix the source template.', [
                'original' => $message,
            ]);
        }

        return $fixed;
    }

    /**
     * Converts common PH local formats into E.164.
     *   "09171234567"   -> "+639171234567"
     *   "9171234567"    -> "+639171234567"
     *   "639171234567"  -> "+639171234567"
     *   "+639171234567" -> "+639171234567" (already fine, passes through)
     *
     * Returns null if the digits don't match any recognizable PH mobile
     * pattern, so callers know not to send rather than firing a bad request.
     */
    private function normalizePhilippineNumber(string $number): ?string
    {
        $digits = preg_replace('/\D/', '', $number);

        if (str_starts_with($digits, '09') && strlen($digits) === 11) {
            return '+63'.substr($digits, 1);
        }

        if (str_starts_with($digits, '639') && strlen($digits) === 12) {
            return '+'.$digits;
        }

        if (str_starts_with($digits, '9') && strlen($digits) === 10) {
            return '+63'.$digits;
        }

        return null;
    }
}