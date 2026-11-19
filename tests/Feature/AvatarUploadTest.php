<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AvatarUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_avatar_successfully(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['is_active' => true]);
        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->image('profile.jpg', 400, 400);

        $response = $this->postJson('/api/account/avatar', [
            'avatar' => $file,
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'avatar_url',
                'user' => ['id', 'name', 'avatar_url', 'identity_masked'],
            ]);

        $avatarUrl = $response->json('avatar_url');
        $this->assertNotNull($avatarUrl);
        $this->assertStringContainsString('/storage/avatars/', $avatarUrl);

        $user->refresh();
        $this->assertEquals($avatarUrl, $user->avatar_url);

        // Verify storage file exists
        $filePath = str_replace('/storage/', '', parse_url($avatarUrl, PHP_URL_PATH));
        Storage::disk('public')->assertExists($filePath);

        // Verify /api/me reflects avatar
        $meResponse = $this->getJson('/api/me');
        $meResponse->assertOk()
            ->assertJsonPath('avatar_url', $avatarUrl);
    }

    public function test_uploading_non_image_or_oversized_file_is_rejected(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['is_active' => true]);
        Sanctum::actingAs($user);

        // Non-image file
        $pdfFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $response = $this->postJson('/api/account/avatar', [
            'avatar' => $pdfFile,
        ]);
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['avatar']);

        // Oversized image > 5MB
        $hugeImage = UploadedFile::fake()->image('huge.png')->size(6000);
        $oversizedResponse = $this->postJson('/api/account/avatar', [
            'avatar' => $hugeImage,
        ]);
        $oversizedResponse->assertUnprocessable()
            ->assertJsonValidationErrors(['avatar']);
    }

    public function test_user_can_delete_their_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['is_active' => true]);
        Sanctum::actingAs($user);

        // Upload first
        $file = UploadedFile::fake()->image('profile.jpg', 300, 300);
        $uploadRes = $this->postJson('/api/account/avatar', ['avatar' => $file]);
        $uploadRes->assertOk();

        $avatarUrl = $uploadRes->json('avatar_url');
        $filePath = str_replace('/storage/', '', parse_url($avatarUrl, PHP_URL_PATH));
        Storage::disk('public')->assertExists($filePath);

        // Delete avatar
        $deleteRes = $this->deleteJson('/api/account/avatar');
        $deleteRes->assertOk()
            ->assertJson([
                'message' => 'Profile picture removed.',
                'avatar_url' => null,
            ]);

        $user->refresh();
        $this->assertNull($user->avatar_url);
        Storage::disk('public')->assertMissing($filePath);
    }

    public function test_uploading_new_avatar_deletes_previous_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['is_active' => true]);
        Sanctum::actingAs($user);

        $file1 = UploadedFile::fake()->image('first.jpg', 200, 200);
        $res1 = $this->postJson('/api/account/avatar', ['avatar' => $file1]);
        $res1->assertOk();
        $path1 = str_replace('/storage/', '', parse_url($res1->json('avatar_url'), PHP_URL_PATH));
        Storage::disk('public')->assertExists($path1);

        $file2 = UploadedFile::fake()->image('second.png', 200, 200);
        $res2 = $this->postJson('/api/account/avatar', ['avatar' => $file2]);
        $res2->assertOk();
        $path2 = str_replace('/storage/', '', parse_url($res2->json('avatar_url'), PHP_URL_PATH));
        Storage::disk('public')->assertExists($path2);

        // Old file must be gone
        Storage::disk('public')->assertMissing($path1);
    }
}
