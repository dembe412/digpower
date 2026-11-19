<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ClientAdministrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_deactivate_and_reactivate_a_client(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true, 'is_active' => true])->save();
        $client = User::factory()->create(['is_active' => true]);
        Sanctum::actingAs($admin);

        $this->postJson("/api/admin/users/{$client->id}/toggle")
            ->assertOk();
        $this->assertFalse($client->fresh()->is_active);

        $this->postJson("/api/admin/users/{$client->id}/toggle")
            ->assertOk();
        $this->assertTrue($client->fresh()->is_active);
    }

    public function test_admin_delete_archives_and_anonymizes_client_records(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true, 'is_active' => true])->save();
        $client = User::factory()->create(['email' => 'client@example.test']);
        Sanctum::actingAs($admin);

        $this->deleteJson("/api/admin/users/{$client->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Client archived and anonymized.');

        $archived = $client->fresh();
        $this->assertNotNull($archived->archived_at);
        $this->assertFalse($archived->is_active);
        $this->assertNull($archived->email);
        $this->assertDatabaseHas('users', ['id' => $client->id]);
    }
}
