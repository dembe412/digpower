<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_phone_registration_creates_a_token_and_accepts_the_required_risk_terms(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'phone' => '15551234567',
            'password' => 'correct-horse-battery',
            'password_confirmation' => 'correct-horse-battery',
            'accept_terms' => true,
            'accept_risk' => true,
        ]);

        $response->assertCreated()->assertJsonStructure(['token', 'user' => ['id', 'identity_masked', 'invite_code']]);

        $this->assertDatabaseHas('users', [
            'phone' => '15551234567',
            'email' => null,
        ]);
    }

    public function test_registration_without_risk_acknowledgement_is_rejected(): void
    {
        $this->postJson('/api/auth/register', [
            'email' => 'member@example.test',
            'password' => 'correct-horse-battery',
            'password_confirmation' => 'correct-horse-battery',
            'accept_terms' => true,
        ])->assertUnprocessable()->assertJsonValidationErrors(['accept_risk']);
    }

    public function test_member_can_sign_in_and_receive_a_sanctum_token(): void
    {
        $user = User::factory()->create(['email' => 'member@example.test', 'password' => 'correct-horse-battery']);

        $this->postJson('/api/auth/login', [
            'identifier' => $user->email,
            'password' => 'correct-horse-battery',
        ])->assertOk()->assertJsonStructure(['token', 'user' => ['id', 'identity_masked']]);
    }
}
