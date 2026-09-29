<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Password must contain at least one capital letter (A-Z). Laravel's built-in
 * Password rule only offers "mixed case" (upper AND lower), so this covers the
 * uppercase-only requirement on its own.
 */
class HasUppercase implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match('/\p{Lu}/u', (string) $value)) {
            $fail('The :attribute must contain at least one uppercase letter.');
        }
    }
}
