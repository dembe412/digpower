<?php

namespace Database\Seeders;

use App\Models\Announcement;
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
        foreach ($tiers as [$lvl, $name, $min, $max, $y, $win]) {
            if ($lvl === 0) {
                continue;
            }
            [$code, $mineralName, $image] = $minerals[$lvl];
            Product::updateOrCreate(['code' => $code], [
                'name' => $mineralName,
                'cost_price' => $min,
                'daily_income' => $win ? ($min * $y / 100 / $win) : 0,
                'duration_days' => $win,
                'image_path' => $image,
                'tagline' => 'Indicative earnings only; all product activation is manually reviewed.',
                'sort_order' => $lvl,
                'is_active' => true,
            ]);
        }

        $admin = User::firstOrCreate(['email' => env('ADMIN_EMAIL', 'admin@example.com')], [
            'name' => 'Admin', 'password' => Hash::make(env('ADMIN_PASSWORD', 'ChangeMe!12345')),
            'is_admin' => true, 'invite_code' => Str::upper(Str::random(6)), 'terms_accepted_at' => now(),
        ]);
        if (! $admin->is_admin || ! $admin->is_super_admin || $admin->admin_role !== 'super_admin') {
            $admin->forceFill([
                'is_admin' => true,
                'is_super_admin' => true,
                'admin_role' => 'super_admin',
            ])->save();
        }

        Announcement::firstOrCreate(['title' => 'Welcome to '.config('platform.name')], ['kind' => 'ticker']);

        Faq::insert([
            ['question' => 'Are returns guaranteed?', 'answer' => 'No. Earnings are distributed pro-rata from revenue the platform actually reports. Percentages shown on plans are indicative targets and can be lower or zero. You can lose money.', 'sort' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['question' => 'When can I withdraw my deposit?', 'answer' => 'Deposits are locked for the plan window. Claimed earnings and referral rewards can be withdrawn at any time, subject to review.', 'sort' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Setting::put('about', ['text' => 'Edit this in the admin panel.']);
        Setting::put('video_intro_url', null);
        Setting::put('app_download_url', null);
        Setting::put('support_contact', ['telegram' => null, 'email' => null]);

        if (ChatMessage::count() === 0 && $admin) {
            $msg1 = ChatMessage::create([
                'type' => 'group',
                'room' => 'community',
                'user_id' => $admin->id,
                'message' => '👋 Welcome to the official DigiPowerX community group chat! Discuss daily earnings, share machine updates, and connect with other miners here.',
                'is_staff' => true,
                'created_at' => now()->subHours(3),
            ]);
            ChatMessage::create([
                'type' => 'group',
                'room' => 'community',
                'user_id' => $admin->id,
                'reply_to_id' => $msg1->id,
                'message' => '💡 Tip: For private balance issues or deposit questions, open your dedicated Private Chat in "My Account" to speak directly with an account manager.',
                'is_staff' => true,
                'created_at' => now()->subHours(2),
            ]);
        }
    }
}
