<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Models\VipLevel;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeding_twice_does_not_duplicate_faqs_or_overwrite_customized_settings(): void
    {
        $this->seed(DatabaseSeeder::class);

        Setting::put('about', ['text' => 'Custom company bio']);

        $this->seed(DatabaseSeeder::class);

        $this->assertSame(2, Faq::count());
        $this->assertSame(8, Product::count());
        $this->assertSame(9, VipLevel::count());
        $this->getJson('/api/about')
            ->assertOk()
            ->assertJsonPath('text', 'Custom company bio');
    }

    public function test_seeder_assigns_admin_flags_for_all_three_roles_and_preserves_existing_passwords(): void
    {
        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::where('email', 'admin@example.com')->firstOrFail();
        $opsAdmin = User::where('email', 'ops@digipowerx.test')->firstOrFail();
        $manager = User::where('email', 'manager@digipowerx.test')->firstOrFail();

        $this->assertTrue($superAdmin->is_admin);
        $this->assertTrue($superAdmin->is_super_admin);
        $this->assertSame('super_admin', $superAdmin->effectiveAdminRole());

        $this->assertTrue($opsAdmin->is_admin);
        $this->assertFalse($opsAdmin->is_super_admin);
        $this->assertSame('admin', $opsAdmin->effectiveAdminRole());

        $this->assertTrue($manager->is_admin);
        $this->assertFalse($manager->is_super_admin);
        $this->assertSame('manager', $manager->effectiveAdminRole());

        $opsAdmin->update(['password' => 'CustomStrongPass!99']);

        $this->seed(DatabaseSeeder::class);

        $this->assertTrue(Hash::check('CustomStrongPass!99', $opsAdmin->fresh()->password));
    }

    public function test_seeder_skips_default_weak_admin_accounts_in_production_when_env_is_unset(): void
    {
        $originalEnv = $this->app['env'];
        $this->app['env'] = 'production';

        try {
            $this->artisan('db:seed', ['--class' => DatabaseSeeder::class, '--force' => true])
                ->assertSuccessful();
        } finally {
            $this->app['env'] = $originalEnv;
        }

        $this->assertSame(0, User::where('is_admin', true)->count());
        $this->assertSame(8, Product::count());
        $this->assertSame(9, VipLevel::count());
        $this->assertSame(2, Faq::count());
    }
}
