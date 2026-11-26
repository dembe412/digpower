<?php

namespace Database\Seeders;

use App\Models\ChatMessage;
use App\Models\Faq;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Models\VipLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tier ranges in UGX. target_yield_pct is an indicative projection only.
        $tiers = [
            [0, 'No equipment', 0, 0, 0, null],
            [1, 'Level 1', 20000, 50000, 25, 188],
            [2, 'Level 2', 50000, 100000, 25, 188],
            [3, 'Level 3', 100000, 250000, 26, 188],
            [4, 'Level 4', 250000, 500000, 26, 188],
            [5, 'Level 5', 500000, 1000000, 27, 188],
            [6, 'Level 6', 1000000, 2500000, 27, 188],
            [7, 'Level 7', 2500000, 5000000, 27, 188],
            [8, 'Level 8', 5000000, 10000000, 27, 188],
        ];
        foreach ($tiers as [$lvl, $name, $min, $max, $y, $win]) {
            VipLevel::updateOrCreate(['level' => $lvl], [
                'name' => $name, 'min_deposit' => $min, 'max_deposit' => $max ?: null,
                'target_yield_pct' => $y, 'window_days' => $win,
                'rebate_rates' => config('platform.referral_rates'),
                'price' => $lvl >= 1 && $lvl <= 3 ? [1 => 50000, 2 => 150000, 3 => 500000][$lvl] : 0,
                'recharge_requirement' => $lvl >= 1 && $lvl <= 3 ? [1 => 20000, 2 => 100000, 3 => 300000][$lvl] : 0,
                'required_level_1_members' => $lvl >= 1 && $lvl <= 3 ? $lvl : 0,
                'required_level_2_members' => $lvl >= 1 && $lvl <= 3 ? max(0, $lvl - 1) : 0,
                'required_level_3_members' => $lvl >= 1 && $lvl <= 3 ? max(0, $lvl - 2) : 0,
                'monthly_salary' => $lvl >= 1 && $lvl <= 3 ? [1 => 15000, 2 => 45000, 3 => 150000][$lvl] : 0,
                'duration_days' => $lvl >= 1 && $lvl <= 3 ? 30 : 30,
                'tagline' => $lvl >= 1 && $lvl <= 3 ? 'Earn monthly salary with a qualified product-owning team.' : null,
                'sort_order' => $lvl,
                'is_active' => $lvl >= 1 && $lvl <= 3,
            ]);
        }

        $minerals = [
            1 => ['BRONZE', 'Bronze', '/images/products/mining/bronze.webp'],
            2 => ['GRAPHITE', 'Graphite', '/images/products/mining/graphite.webp'],
            3 => ['SILVER', 'Silver', '/images/products/mining/silver.webp'],
            4 => ['GOLD', 'Gold', '/images/products/mining/gold.webp'],
            5 => ['DIAMOND', 'Diamond', '/images/products/mining/diamond.webp'],
            6 => ['MONACO-DIAMOND', 'Monaco Diamond', '/images/products/mining/monaco-diamond.webp'],
            7 => ['COLORED-DIAMOND', 'Colored Diamond', '/images/products/mining/colored-diamond.webp'],
            8 => ['GREEN-MINERAL', 'Green Mineral', '/images/products/mining/green-mineral.webp'],
        ];
        if (Product::count() === 0) {
            foreach ($tiers as [$lvl, $name, $min, $max, $y, $win]) {
                if ($lvl === 0) {
                    continue;
                }
                [$code, $mineralName, $image] = $minerals[$lvl];
                Product::create([
                    'code' => $code,
                    'name' => $mineralName,
                    'cost_price' => $min,
                    'daily_income' => $win ? ($min * $y / 100 / $win) : 0,
                    'duration_days' => $win,
                    'image_path' => $image,
                    'tagline' => 'Manual activation after payment verification.',
                    'sort_order' => $lvl,
                    'is_active' => true,
                ]);
            }
        }

        $isProduction = app()->isProduction();

        // Remove old test staff accounts before seeding the 3 new staff
        $oldStaff = User::whereIn('email', [
            'admin@example.com',
            'admin@digipowerx.test',
            'ops@digipowerx.test',
            'manager@digipowerx.test',
            'edrinealwin@gmail.com',
        ])->orWhere('name', 'like', '%edrine%')
            ->orWhere('phone', 'like', '%edrine%')
            ->get();
        foreach ($oldStaff as $old) {
            $old->update(['invite_code' => 'OLD_'.$old->id.'_'.Str::random(4)]);
            $old->delete();
        }

        $adminId = env('ADMIN_IDENTIFIER', env('ADMIN_EMAIL'));
        if ($adminId === 'edrinealwin@gmail.com') {
            $adminId = null;
        }

        $superAdmin = $this->seedAdminAccount(
            identifier: $isProduction ? $adminId : ($adminId ?: 'brandon'),
            password: $isProduction ? env('ADMIN_PASSWORD') : env('ADMIN_PASSWORD', 'brandon256@#'),
            name: 'brandon',
            role: 'super_admin',
            isSuperAdmin: true,
            inviteCode: 'BRND01',
        );

        $opsAdmin = $this->seedAdminAccount(
            identifier: $isProduction ? env('OPS_ADMIN_IDENTIFIER', env('OPS_ADMIN_EMAIL')) : env('OPS_ADMIN_IDENTIFIER', env('OPS_ADMIN_EMAIL', 'admin@digpowerX')),
            password: $isProduction ? env('OPS_ADMIN_PASSWORD') : env('OPS_ADMIN_PASSWORD', 'peter256@#'),
            name: 'admin@digpowerX',
            role: 'admin',
            isSuperAdmin: false,
            inviteCode: 'ADM001',
        );

        $customerManager = $this->seedAdminAccount(
            identifier: $isProduction ? env('MANAGER_IDENTIFIER', env('MANAGER_EMAIL')) : env('MANAGER_IDENTIFIER', env('MANAGER_EMAIL', 'jydan')),
            password: $isProduction ? env('MANAGER_PASSWORD') : env('MANAGER_PASSWORD', 'jydan256@#'),
            name: 'jydan',
            role: 'manager',
            isSuperAdmin: false,
            inviteCode: 'MGR001',
        );

        // Reassign any orphaned system chat messages to the super admin
        if ($superAdmin) {
            ChatMessage::whereNull('user_id')->orWhere('user_id', 0)->update(['user_id' => $superAdmin->id]);
        }

        // Revoke admin & staff privileges from any old/unwanted accounts
        $validAdminIds = array_filter([$superAdmin?->id, $opsAdmin?->id, $customerManager?->id]);
        User::whereNotIn('id', $validAdminIds)
            ->where(fn ($q) => $q->where('is_admin', true)->orWhereNotNull('admin_role'))
            ->update([
                'is_admin' => false,
                'is_super_admin' => false,
                'admin_role' => null,
            ]);

        $admin = $superAdmin;

        $defaultFaqs = [
            [
                'question' => 'How do mineral mining products work?',
                'answer' => 'Choose an equipment plan, pay manually via Mobile Money or supported methods, and submit your transaction reference. Our administrators verify the payment to activate your daily returns.',
                'sort' => 1,
                'is_active' => true,
            ],
            [
                'question' => 'When can I withdraw my deposit and earnings?',
                'answer' => 'Claimed daily mining earnings and referral rewards can be withdrawn directly to your Mobile Money account at any time, subject to normal review.',
                'sort' => 2,
                'is_active' => true,
            ],
        ];
        foreach ($defaultFaqs as $faq) {
            Faq::firstOrCreate(['question' => $faq['question']], $faq);
        }

        $defaultSettings = [
            'about' => ['text' => config('platform.name').' provides mineral mining products, transparent manual administration, and community referral rewards.'],
            'video_intro_url' => null,
            'app_download_url' => null,
            'support_contact' => ['telegram' => null, 'email' => null],
        ];
        foreach ($defaultSettings as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }

        $welcomeAuthor = $admin ?? User::where('is_admin', true)->first();

        if (ChatMessage::count() === 0 && $welcomeAuthor) {
            $msg1 = ChatMessage::create([
                'type' => 'group',
                'room' => 'community',
                'user_id' => $welcomeAuthor->id,
                'message' => '👋 Welcome to the official DigiPowerX community group chat! Discuss daily earnings, share machine updates, and connect with other miners here.',
                'is_staff' => true,
                'created_at' => now()->subHours(3),
            ]);
            ChatMessage::create([
                'type' => 'group',
                'room' => 'community',
                'user_id' => $welcomeAuthor->id,
                'reply_to_id' => $msg1->id,
                'message' => '💡 Tip: For private balance issues or deposit questions, open your dedicated Private Chat in "My Account" to speak directly with an account manager.',
                'is_staff' => true,
                'created_at' => now()->subHours(2),
            ]);
        }
    }

    private function seedAdminAccount(
        ?string $identifier,
        ?string $password,
        string $name,
        string $role,
        bool $isSuperAdmin,
        ?string $inviteCode = null,
    ): ?User {
        if (! $identifier || ! $password) {
            return null;
        }

        $user = User::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->orWhere('name', $identifier)
            ->orWhere('name', $name)
            ->first();

        $isEmail = str_contains($identifier, '@');

        if (! $user) {
            $user = new User([
                'name' => $name,
                'email' => $isEmail ? $identifier : null,
                'phone' => ! $isEmail ? $identifier : null,
                'password' => Hash::make($password),
                'invite_code' => $inviteCode ?? Str::upper(Str::random(6)),
                'terms_accepted_at' => now(),
                'is_active' => true,
            ]);
            $user->forceFill([
                'is_admin' => true,
                'is_super_admin' => $isSuperAdmin,
                'admin_role' => $role,
            ])->save();

            return $user;
        }

        $attributes = [
            'name' => $name,
            'is_admin' => true,
            'is_super_admin' => $isSuperAdmin,
            'admin_role' => $role,
            'is_active' => true,
        ];

        if (! $user->is_admin) {
            $attributes['password'] = Hash::make($password);
        }

        $user->forceFill($attributes)->save();

        return $user;
    }
}
