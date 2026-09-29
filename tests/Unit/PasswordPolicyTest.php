<?php

use App\Support\TemporaryPassword;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

// Password policy: 8+ characters, at least 1 number, 1 symbol, 1 uppercase letter.
// Uses the app's boot (AppServiceProvider sets Password::defaults()).
uses(Tests\TestCase::class);

function passesPolicy(string $password): bool
{
    return Validator::make(['password' => $password], ['password' => [Password::defaults()]])->passes();
}

test('a password meeting every requirement is accepted', function () {
    expect(passesPolicy('Dorm-life2026'))->toBeTrue();
});

test('passwords missing a requirement are rejected', function (string $password) {
    expect(passesPolicy($password))->toBeFalse();
})->with([
    'too short' => 'Ab1!',
    'no uppercase' => 'dorm-life2026',
    'no number' => 'Dorm-lifeNow',
    'no symbol' => 'DormLife2026',
]);

test('generated temporary passwords always meet the policy', function () {
    foreach (range(1, 200) as $_) {
        expect(passesPolicy(TemporaryPassword::generate()))->toBeTrue();
    }
});
