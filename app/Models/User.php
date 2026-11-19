<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $guarded = ['id', 'is_admin'];

    protected $hidden = ['password', 'remember_token', 'pin_hash', 'totp_secret'];

    protected $attributes = [
        'is_admin' => false,
        'is_super_admin' => false,
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'totp_secret' => 'encrypted',
            'totp_enabled' => 'boolean',
            'is_admin' => 'boolean',
            'is_super_admin' => 'boolean',
            'is_active' => 'boolean',
            'show_in_public_log' => 'boolean',
            'email_verified_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin || $this->admin_role === 'super_admin';
    }

    public function isOperationsAdmin(): bool
    {
        return (bool) $this->is_admin && ($this->admin_role === 'admin' || (! $this->is_super_admin && ! $this->admin_role));
    }

    public function isCustomerManager(): bool
    {
        return (bool) $this->is_admin && $this->admin_role === 'manager';
    }

    public function effectiveAdminRole(): ?string
    {
        if (! $this->is_admin) {
            return null;
        }
        if ($this->isSuperAdmin()) {
            return 'super_admin';
        }
        if ($this->admin_role === 'manager') {
            return 'manager';
        }

        return 'admin';
    }

    /**
     * Super Admin has overarching oversight but does not execute operational or financial mutations.
     * Only Operations Admin (role: admin) can modify daily operations.
     */
    public function canModifyOperations(): bool
    {
        return $this->isOperationsAdmin();
    }

    /**
     * Operations Admin and Super Admin can view transactions (monitoring); Customer Manager cannot.
     */
    public function canAccessTransactions(): bool
    {
        return $this->isOperationsAdmin() || $this->isSuperAdmin();
    }

    /**
     * Only Operations Admin can execute financial/transactional mutations (confirming/rejecting deposits, withdrawals, purchases).
     */
    public function canMutateTransactions(): bool
    {
        return $this->isOperationsAdmin();
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referrer_id');
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function miningRecords(): HasMany
    {
        return $this->hasMany(MiningRecord::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function vipPurchases(): HasMany
    {
        return $this->hasMany(VipPurchase::class);
    }

    public function maskedIdentity(): string
    {
        return self::mask($this->email ?: (string) $this->phone);
    }

    public static function mask(string $v): string
    {
        if (str_contains($v, '@')) {
            [$l, $d] = explode('@', $v, 2);

            return substr($l, 0, 2).'****'.substr($l, -1).'@'.$d;
        }

        return substr($v, 0, 3).'****'.substr($v, -2);
    }
}
