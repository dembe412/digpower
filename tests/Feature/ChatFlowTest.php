<?php

namespace Tests\Feature;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChatFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_user_can_view_group_messages(): void
    {
        $author = User::factory()->create([
            'name' => 'John Doe',
            'is_active' => true,
            'avatar_url' => '/storage/avatars/test.webp',
        ]);
        ChatMessage::create([
            'type' => 'group',
            'room' => 'community',
            'user_id' => $author->id,
            'message' => 'Hello everyone!',
        ]);

        $response = $this->getJson('/api/chat/messages?type=group');

        $response->assertOk()
            ->assertJsonPath('type', 'group')
            ->assertJsonPath('room', 'community')
            ->assertJsonCount(1, 'messages')
            ->assertJsonPath('messages.0.message', 'Hello everyone!')
            ->assertJsonPath('messages.0.user_name', 'John Doe')
            ->assertJsonPath('messages.0.user_avatar_url', '/storage/avatars/test.webp');
    }

    public function test_authenticated_user_can_send_group_message_with_mentions(): void
    {
        $user = User::factory()->create(['name' => 'Alice', 'is_active' => true]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/chat/messages', [
            'type' => 'group',
            'message' => 'Hey @Admin and @Bob, check this out!',
        ]);

        $response->assertCreated()
            ->assertJsonPath('chat_message.user_name', 'Alice')
            ->assertJsonPath('chat_message.message', 'Hey @Admin and @Bob, check this out!')
            ->assertJsonPath('chat_message.mentions', ['Admin', 'Bob']);

        $this->assertDatabaseHas('chat_messages', [
            'user_id' => $user->id,
            'type' => 'group',
            'room' => 'community',
            'message' => 'Hey @Admin and @Bob, check this out!',
        ]);
    }

    public function test_user_can_reply_to_a_chat_message(): void
    {
        $author = User::factory()->create(['name' => 'Charlie', 'is_active' => true]);
        $original = ChatMessage::create([
            'type' => 'group',
            'room' => 'community',
            'user_id' => $author->id,
            'message' => 'What is the daily ROI?',
        ]);

        $responder = User::factory()->create(['name' => 'Dave', 'is_active' => true]);
        Sanctum::actingAs($responder);

        $response = $this->postJson('/api/chat/messages', [
            'type' => 'group',
            'reply_to_id' => $original->id,
            'message' => 'It depends on your product level!',
        ]);

        $response->assertCreated()
            ->assertJsonPath('chat_message.reply_to.id', $original->id)
            ->assertJsonPath('chat_message.reply_to.user_name', 'Charlie')
            ->assertJsonPath('chat_message.reply_to.message', 'What is the daily ROI?');
    }

    public function test_user_can_share_picture_in_chat(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['name' => 'Eva', 'is_active' => true]);
        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->image('payment_proof.png', 400, 300);

        $response = $this->post('/api/chat/messages', [
            'type' => 'group',
            'message' => 'Here is my transaction confirmation',
            'image' => $file,
        ], ['Accept' => 'application/json']);

        $response->assertCreated()
            ->assertJsonPath('chat_message.user_name', 'Eva');

        $imageUrl = $response->json('chat_message.image_url');
        $this->assertNotNull($imageUrl);
        $this->assertStringContainsString('chat_attachments', $imageUrl);

        $storedPath = str_replace('/storage/', '', $imageUrl);
        Storage::disk('public')->assertExists($storedPath);
    }

    public function test_user_can_have_a_private_chat(): void
    {
        $user = User::factory()->create(['name' => 'Frank', 'is_active' => true]);
        $admin = User::factory()->create(['is_admin' => true, 'is_active' => true, 'name' => 'Admin Support']);
        Sanctum::actingAs($user);

        // Send private message
        $response = $this->postJson('/api/chat/messages', [
            'type' => 'private',
            'message' => 'Can you help me with my VIP qualification?',
        ]);

        $response->assertCreated()
            ->assertJsonPath('chat_message.type', 'private')
            ->assertJsonPath('chat_message.room', 'user_'.$user->id);

        // Fetch private messages as Frank
        $this->getJson('/api/chat/messages?type=private')
            ->assertOk()
            ->assertJsonPath('type', 'private')
            ->assertJsonPath('room', 'user_'.$user->id)
            ->assertJsonCount(1, 'messages');

        // Another user cannot see Frank's private messages
        $otherUser = User::factory()->create(['is_active' => true]);
        Sanctum::actingAs($otherUser);
        $this->getJson('/api/chat/messages?type=private')
            ->assertOk()
            ->assertJsonPath('room', 'user_'.$otherUser->id)
            ->assertJsonCount(0, 'messages');

        // Admin CAN view Frank's private messages
        Sanctum::actingAs($admin);
        $this->getJson('/api/chat/messages?type=private&target_user_id='.$user->id)
            ->assertOk()
            ->assertJsonPath('room', 'user_'.$user->id)
            ->assertJsonCount(1, 'messages');
    }

    public function test_user_can_delete_their_own_message(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $message = ChatMessage::create([
            'type' => 'group',
            'room' => 'community',
            'user_id' => $user->id,
            'message' => 'Oops typo',
        ]);

        Sanctum::actingAs($user);
        $this->deleteJson('/api/chat/messages/'.$message->id)
            ->assertOk();

        $this->assertDatabaseMissing('chat_messages', ['id' => $message->id]);
    }
}
