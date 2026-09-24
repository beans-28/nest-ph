<?php

namespace App\Services;

use App\Models\Review;

/**
 * Automatic review filter. Flagged reviews are HIDDEN (status = hidden),
 * never deleted, so the admin can reverse a false positive and the tenant
 * can't resubmit (reviews.tenant_id is unique).
 */
class ReviewModerationService
{
    private const LEET_MAP = [
        '0' => 'o', '1' => 'i', '3' => 'e', '4' => 'a', '5' => 's',
        '7' => 't', '@' => 'a', '$' => 's', '!' => 'i',
    ];

    /**
     * Returns a list of human-readable reasons. Empty list = clean.
     */
    public function scan(?string $text): array
    {
        $text = trim((string) $text);
        if ($text === '') {
            return [];
        }

        $reasons = [];

        $badWords = $this->findBlockedWords($text);
        if ($badWords) {
            $reasons[] = 'Inappropriate language (' . implode(', ', $badWords) . ')';
        }

        return array_merge($reasons, $this->findSpamSignals($text));
    }

    /**
     * Runs the filter on a review and saves the result. Reviews an admin
     * has already acted on are left alone, so a manual decision always wins.
     */
    public function autoModerate(Review $review): Review
    {
        if ($review->moderated_by) {
            return $review;
        }

        $reasons = $this->scan($review->comment);

        if ($reasons) {
            $review->status = 'hidden';
            $review->flag_reasons = $reasons;
            $review->moderated_at = now();
        } elseif ($review->status === 'hidden') {
            // Was auto-hidden before, but passes the current rules
            // (e.g. a word was removed from the list).
            $review->status = 'published';
            $review->flag_reasons = null;
        }

        $review->save();

        return $review;
    }

    private function normalize(string $text): string
    {
        $t = mb_strtolower($text);
        $t = strtr($t, self::LEET_MAP);

        // "gaaaaago" -> "gago" (3+ repeats collapse; normal double letters stay)
        return preg_replace('/(\p{L})\1{2,}/u', '$1', $t);
    }

    private function findBlockedWords(string $text): array
    {
        $normal = $this->normalize($text);
        $squashed = preg_replace('/[^\p{L}]/u', '', $normal);
        $found = [];

        foreach (config('review_moderation.blocked_words', []) as $word) {
            $word = mb_strtolower(trim($word));
            if ($word === '') {
                continue;
            }

            // Whole-word match; spaces in the entry also match "", "-", "." etc.
            $parts = array_map(fn ($p) => preg_quote($p, '/'), explode(' ', $word));
            $pattern = '/(?<!\p{L})' . implode('[^\p{L}]*', $parts) . '(?!\p{L})/u';

            $hit = preg_match($pattern, $normal) === 1;

            // Catch spaced-out tricks like "t a n g i n a", only for long
            // words so short ones don't cause false positives.
            $joined = str_replace(' ', '', $word);
            if (! $hit && mb_strlen($joined) >= 7 && str_contains($squashed, $joined)) {
                $hit = true;
            }

            if ($hit) {
                $found[] = $word;
            }
        }

        return array_values(array_unique($found));
    }

    private function findSpamSignals(string $text): array
    {
        $signals = [];

        // Links. ".ph" is left out on purpose so "NEST.PH" itself isn't flagged.
        if (preg_match('~(https?://|www\.)~i', $text)
            || preg_match('~\b[a-z0-9-]+\.(com|net|org|xyz|io|info|shop|site|link|ly)\b~i', $text)) {
            $signals[] = 'Contains a link';
        }

        if (preg_match('/[\w.+-]+@[\w-]+\.[\w.]+/', $text)) {
            $signals[] = 'Contains an email address';
        }

        // Phone numbers: any run with 10+ digits (09xx..., +639xx...)
        if (preg_match_all('/\+?\d[\d\s().-]{8,}\d/', $text, $m)) {
            foreach ($m[0] as $candidate) {
                if (strlen(preg_replace('/\D/', '', $candidate)) >= 10) {
                    $signals[] = 'Contains a phone number';
                    break;
                }
            }
        }

        // Same character 6+ times in a row: "!!!!!!", "zzzzzzz"
        if (preg_match('/(\S)\1{5,}/u', $text)) {
            $signals[] = 'Repeated characters';
        }

        // One word making up more than half of a 6+ word comment
        $words = preg_split('/\s+/u', trim(preg_replace('/[^\p{L}\p{N}\s]/u', '', mb_strtolower($text))));
        $words = array_filter($words);
        if (count($words) >= 6) {
            $top = max(array_count_values($words));
            if ($top / count($words) > 0.5) {
                $signals[] = 'Repeated words';
            }
        }

        // Mostly capital letters (only checked when there's enough text)
        $letters = preg_match_all('/\p{L}/u', $text);
        $upper = preg_match_all('/\p{Lu}/u', $text);
        if ($letters >= 20 && $upper / $letters > 0.7) {
            $signals[] = 'Mostly capital letters';
        }

        // Keyboard mashing: 8+ letter word with no vowels ("sdfghjkl")
        if (preg_match('/\b[b-df-hj-np-tv-xz]{8,}\b/i', $text)) {
            $signals[] = 'Looks like random typing';
        }

        return $signals;
    }
}