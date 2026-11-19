<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\TotpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $r)
    {
        $data = $r->validate([
            'email' => ['nullable', 'email', 'max:190', 'required_without:phone', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20', 'required_without:email', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'invite_code' => [config('platform.invite_code_required') ? 'required' : 'nullable', 'string', 'exists:users,invite_code'],
            'accept_terms' => ['accepted'],
            'accept_risk' => ['accepted'], // user acknowledges returns are not guaranteed
        ]);

        $referrer = isset($data['invite_code']) ? User::where('invite_code', $data['invite_code'])->first() : null;

        $user = User::create([
            'name' => $data['email'] ?? $data['phone'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'referrer_id' => $referrer?->id,
            'invite_code' => $this->uniqueCode(),
            'terms_accepted_at' => now(),
            'locale' => $r->input('locale', 'en'),
        ]);
        AuditLog::record($user->id, 'auth.register', $user);

        return response()->json(['token' => $user->createToken('app')->plainTextToken, 'user' => $this->profile($user)], 201);
    }

    public function login(Request $r)
    {
        $r->validate(['identifier' => 'required|string', 'password' => 'required|string', 'totp_code' => 'nullable|string']);

        $user = User::where('email', $r->identifier)->orWhere('phone', $r->identifier)->first();
        if (! $user || Hash::check($r->password, $user->password) === false || ! $user->is_active || $user->archived_at) {
            throw ValidationException::withMessages(['identifier' => 'Invalid credentials.']);
        }
        if ($user->totp_enabled && ! app(TotpService::class)->verify($user->totp_secret, (string) $r->totp_code)) {
            throw ValidationException::withMessages(['totp_code' => 'Authenticator code required or invalid.']);
        }

        $user->update(['last_login_ip' => $r->ip()]);
        AuditLog::record($user->id, 'auth.login', $user);

        return response()->json(['token' => $user->createToken('app')->plainTextToken, 'user' => $this->profile($user)]);
    }

    public function logout(Request $r)
    {
        $r->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $r)
    {
        return response()->json($this->profile($r->user()));
    }

    public function forgotPassword(Request $r)
    {
        $r->validate(['email' => 'required|email']);
        Password::sendResetLink($r->only('email'));

        return response()->json(['message' => 'If the account exists, a reset link has been sent.']); // no enumeration
    }

    public function resetPassword(Request $r)
    {
        $r->validate(['token' => 'required', 'email' => 'required|email', 'password' => 'required|min:8|confirmed']);
        $status = Password::reset($r->only('email', 'password', 'password_confirmation', 'token'), function (User $u, string $p) {
            $u->forceFill(['password' => $p])->save();
            $u->tokens()->delete();
        });

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password reset'])
            : response()->json(['message' => 'Invalid token or email'], 422);
    }

    private function uniqueCode(): string
    {
        do {
            $c = (string) random_int(100000, 999999);
        } while (User::where('invite_code', $c)->exists());

        return $c;
    }

    private function profile(User $u): array
    {
        return [
            'id' => $u->id,
            'avatar_url' => $u->avatar_url,
            'identity_masked' => $u->maskedIdentity(),
            'invite_code' => $u->invite_code,
            'invite_link' => rtrim(config('platform.frontend_url'), '/').'/#/reg?invite_code='.$u->invite_code,
            'has_pin' => (bool) $u->pin_hash,
            'totp_enabled' => $u->totp_enabled,
            'locale' => $u->locale,
            'is_admin' => $u->is_admin,
            'is_super_admin' => $u->is_super_admin,
            'admin_role' => $u->effectiveAdminRole(),
            'can_access_transactions' => $u->canAccessTransactions(),
            'can_modify_operations' => $u->canModifyOperations(),
            'can_manage_admins' => $u->isSuperAdmin(),
            'invest_balance' => (string) $u->invest_balance,
            'commission_balance' => (string) $u->commission_balance,
            'usd_balance' => (string) ($u->usd_balance ?? 0),
            'eur_balance' => (string) ($u->eur_balance ?? 0),
        ];
    }
}
