<?php

namespace Tests\Feature;

use App\Models\Deposit;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaginationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_clients_endpoint_paginates_users(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_super_admin' => true,
            'is_active' => true,
        ]);
        Sanctum::actingAs($admin);

        User::factory()->count(35)->create(['is_admin' => false, 'is_active' => true]);

        $page1 = $this->getJson('/api/admin/users?page=1');
        $page1->assertOk()
            ->assertJsonPath('clients.current_page', 1)
            ->assertJsonPath('clients.per_page', 30)
            ->assertJsonPath('clients.total', 35)
            ->assertJsonPath('clients.last_page', 2)
            ->assertJsonCount(30, 'clients.data');

        $page2 = $this->getJson('/api/admin/users?page=2');
        $page2->assertOk()
            ->assertJsonPath('clients.current_page', 2)
            ->assertJsonCount(5, 'clients.data');
    }

    public function test_admin_deposits_endpoint_paginates_and_filters_status(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'admin_role' => 'admin',
            'is_active' => true,
        ]);
        Sanctum::actingAs($admin);

        $client = User::factory()->create(['is_admin' => false]);

        for ($i = 0; $i < 35; $i++) {
            Deposit::create([
                'user_id' => $client->id,
                'amount' => 10000 + $i,
                'payment_method' => 'mtn',
                'status' => 'pending',
                'txid' => 'TX_PENDING_'.$i,
            ]);
        }

        Deposit::create([
            'user_id' => $client->id,
            'amount' => 50000,
            'payment_method' => 'mtn',
            'status' => 'confirmed',
            'txid' => 'TX_CONFIRMED_1',
            'confirmed_at' => now(),
        ]);

        $pendingPage1 = $this->getJson('/api/admin/deposits?status=pending&page=1');
        $pendingPage1->assertOk()
            ->assertJsonPath('current_page', 1)
            ->assertJsonPath('per_page', 30)
            ->assertJsonPath('total', 35)
            ->assertJsonCount(30, 'data');

        $pendingPage2 = $this->getJson('/api/admin/deposits?status=pending&page=2');
        $pendingPage2->assertOk()
            ->assertJsonPath('current_page', 2)
            ->assertJsonCount(5, 'data');

        $confirmed = $this->getJson('/api/admin/deposits?status=confirmed&page=1');
        $confirmed->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonCount(1, 'data');
    }

    public function test_statements_endpoint_paginates_ledger_entries(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        Sanctum::actingAs($user);

        for ($i = 0; $i < 35; $i++) {
            $user->transactions()->create([
                'wallet' => 'invest',
                'type' => 'daily_yield',
                'amount' => 100 + $i,
                'balance_after' => 1000 + $i,
            ]);
        }

        $page1 = $this->getJson('/api/statements?page=1');
        $page1->assertOk()
            ->assertJsonPath('current_page', 1)
            ->assertJsonPath('per_page', 30)
            ->assertJsonPath('total', 35)
            ->assertJsonCount(30, 'data');

        $page2 = $this->getJson('/api/statements?page=2');
        $page2->assertOk()
            ->assertJsonPath('current_page', 2)
            ->assertJsonCount(5, 'data');
    }

    public function test_team_level_members_paginates(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        Sanctum::actingAs($user);

        for ($i = 0; $i < 25; $i++) {
            User::factory()->create([
                'referrer_id' => $user->id,
                'is_active' => true,
            ]);
        }

        $page1 = $this->getJson('/api/team/level/1?page=1');
        $page1->assertOk()
            ->assertJsonPath('current_page', 1)
            ->assertJsonPath('per_page', 20)
            ->assertJsonPath('total', 25)
            ->assertJsonCount(20, 'data');

        $page2 = $this->getJson('/api/team/level/1?page=2');
        $page2->assertOk()
            ->assertJsonPath('current_page', 2)
            ->assertJsonCount(5, 'data');
    }

    public function test_user_deposits_and_withdrawals_history_endpoints_paginate(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        Sanctum::actingAs($user);

        for ($i = 0; $i < 25; $i++) {
            Deposit::create([
                'user_id' => $user->id,
                'amount' => 5000 + $i,
                'payment_method' => 'mtn',
                'status' => 'confirmed',
                'txid' => 'DEP_'.$i,
            ]);
            Withdrawal::create([
                'user_id' => $user->id,
                'wallet' => 'invest',
                'amount' => 5000 + $i,
                'fee' => 0,
                'net_amount' => 5000 + $i,
                'network' => 'mtn',
                'address' => '0770000000',
                'status' => 'completed',
            ]);
        }

        $depPage1 = $this->getJson('/api/deposits?page=1');
        $depPage1->assertOk()
            ->assertJsonPath('current_page', 1)
            ->assertJsonPath('per_page', 20)
            ->assertJsonPath('total', 25)
            ->assertJsonCount(20, 'data');

        $withPage1 = $this->getJson('/api/withdrawals?page=1');
        $withPage1->assertOk()
            ->assertJsonPath('current_page', 1)
            ->assertJsonPath('per_page', 20)
            ->assertJsonPath('total', 25)
            ->assertJsonCount(20, 'data');
    }
}
