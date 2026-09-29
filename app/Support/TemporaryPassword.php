<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Random temporary passwords (new admins, new tenants, approved applicants)
 * that always satisfy the password policy: 8+ characters with at least one
 * uppercase letter, one number and one symbol. Plain Str::random() is only
 * letters and numbers, so it could produce a password the policy rejects.
 */
class TemporaryPassword
{
    public static function generate(int $length = 12): string
    {
        // Leaves out look-alike characters (0/O, 1/l/I) since these are
        // read from an email and typed in by hand.
        $upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower = 'abcdefghijkmnopqrstuvwxyz';
        $digits = '23456789';
        $symbols = '!@#$%&*?';

        $pick = fn (string $set) => $set[random_int(0, strlen($set) - 1)];

        $chars = [$pick($upper), $pick($lower), $pick($digits), $pick($symbols)];
        $all = $upper.$lower.$digits.$symbols;
        while (count($chars) < max($length, 8)) {
            $chars[] = $pick($all);
        }

        // Shuffle so the required characters aren't always in the same spots.
        for ($i = count($chars) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$chars[$i], $chars[$j]] = [$chars[$j], $chars[$i]];
        }

        return implode('', $chars);
    }
}
