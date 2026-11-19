<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\UserNotification;
use App\Services\TotpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AccountController extends Controller
{
    use Concerns;

    public function changePassword(Request $r)
    {
        $r->validate(['current_password' => 'required', 'password' => 'required|min:8|confirmed']);
        $u = $r->user();
        if (! Hash::check($r->current_password, $u->password)) {
            throw ValidationException::withMessages(['current_password' => 'Incorrect password.']);
        }
        $this->requireSecurity($r, $u, needPin: false);
        $u->update(['password' => $r->password]);
        $u->tokens()->where('id', '!=', $u->currentAccessToken()->id)->delete();
        AuditLog::record($u->id, 'account.password_changed');

        return response()->json(['message' => 'Password updated']);
    }

    public function setPin(Request $r)
    {
        $r->validate(['new_pin' => ['required', 'digits:6'], 'current_pin' => 'nullable|digits:6', 'password' => 'required']);
        $u = $r->user();
        if (! Hash::check($r->password, $u->password)) {
            throw ValidationException::withMessages(['password' => 'Incorrect password.']);
        }
        if ($u->pin_hash) {
            $r->merge(['pin' => $r->current_pin]);
            $this->requireSecurity($r, $u);
        }
        $u->update(['pin_hash' => Hash::make($r->new_pin)]);
        AuditLog::record($u->id, 'account.pin_set');

        return response()->json(['message' => 'Security PIN saved']);
    }

    public function totpSetup(Request $r, TotpService $totp)
    {
        $u = $r->user();
        abort_if($u->totp_enabled, 422, 'Already enabled');
        $secret = $totp->generateSecret();
        $u->update(['totp_secret' => $secret]);

        return response()->json(['secret' => $secret, 'otpauth_uri' => $totp->uri($secret, $u->email ?: $u->phone)]);
    }

    public function totpEnable(Request $r, TotpService $totp)
    {
        $r->validate(['code' => 'required|string']);
        $u = $r->user();
        abort_unless($u->totp_secret && $totp->verify($u->totp_secret, $r->code), 422, 'Invalid code');
        $u->update(['totp_enabled' => true]);
        AuditLog::record($u->id, 'account.totp_enabled');

        return response()->json(['message' => 'Authenticator enabled']);
    }

    public function totpDisable(Request $r)
    {
        $u = $r->user();
        $this->requireSecurity($r, $u);
        $u->update(['totp_enabled' => false, 'totp_secret' => null]);
        AuditLog::record($u->id, 'account.totp_disabled');

        return response()->json(['message' => 'Authenticator disabled']);
    }

    public function updateAvatar(Request $r): JsonResponse
    {
        $r->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $user = $r->user();
        $file = $r->file('avatar');

        if ($user->avatar_url && str_contains($user->avatar_url, '/storage/avatars/')) {
            $oldPath = str_replace('/storage/', '', parse_url($user->avatar_url, PHP_URL_PATH) ?? '');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $path = $file->store('avatars', 'public');
        $url = Storage::url($path);

        $user->update(['avatar_url' => $url]);
        AuditLog::record($user->id, 'account.avatar_updated');

        return response()->json([
            'message' => 'Profile picture updated successfully.',
            'avatar_url' => $url,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'avatar_url' => $url,
                'identity_masked' => $user->maskedIdentity(),
            ],
        ]);
    }

    public function deleteAvatar(Request $r): JsonResponse
    {
        $user = $r->user();
        if ($user->avatar_url && str_contains($user->avatar_url, '/storage/avatars/')) {
            $oldPath = str_replace('/storage/', '', parse_url($user->avatar_url, PHP_URL_PATH) ?? '');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $user->update(['avatar_url' => null]);
        AuditLog::record($user->id, 'account.avatar_deleted');

        return response()->json([
            'message' => 'Profile picture removed.',
            'avatar_url' => null,
        ]);
    }

    public function preferences(Request $r)
    {
        $d = $r->validate(['locale' => 'sometimes|string|max:8', 'show_in_public_log' => 'sometimes|boolean', 'avatar_url' => 'sometimes|nullable|url|max:500']);
        $r->user()->update($d);

        return response()->json(['message' => 'Saved']);
    }

    public function notifications(Request $r)
    {
        return UserNotification::where('user_id', $r->user()->id)->latest()->paginate(20);
    }

    public function markNotificationsRead(Request $r)
    {
        UserNotification::where('user_id', $r->user()->id)->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['message' => 'ok']);
    }
}
