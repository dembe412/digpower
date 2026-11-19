<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TotpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/** Shared PIN / 2FA gate for sensitive actions (withdraw, transfer, security changes). */
trait Concerns
{
    protected function requireSecurity(Request $request, User $user, bool $needPin = true): void
    {
        $key = 'sec:'.$user->id;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages(['pin' => 'Too many attempts. Try again later.']);
        }

        if ($needPin) {
            if (! $user->pin_hash) {
                throw ValidationException::withMessages(['pin' => 'Set a security PIN first.']);
            }
            if (! Hash::check((string) $request->input('pin'), $user->pin_hash)) {
                RateLimiter::hit($key, 900);
                throw ValidationException::withMessages(['pin' => 'Incorrect PIN.']);
            }
        }

        if ($user->totp_enabled && ! app(TotpService::class)->verify($user->totp_secret, (string) $request->input('totp_code'))) {
            RateLimiter::hit($key, 900);
            throw ValidationException::withMessages(['totp_code' => 'Invalid authenticator code.']);
        }

        RateLimiter::clear($key);
    }
}
