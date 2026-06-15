<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AvatarValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that non-image file is rejected.
     */
    public function test_avatar_validation_rejects_non_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $file,
        ]);

        $response->assertSessionHasErrors(['avatar']);
        $this->assertNull($user->fresh()->avatar);
    }

    /**
     * Test that file larger than 20 MB is rejected.
     */
    public function test_avatar_validation_rejects_file_larger_than_20mb(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->image('huge-avatar.jpg')->size(21000); // 21 MB in KB

        $response = $this->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $file,
        ]);

        $response->assertSessionHasErrors(['avatar']);
        $this->assertNull($user->fresh()->avatar);
    }

    /**
     * Test that avatar upload works with valid image.
     */
    public function test_avatar_accepts_valid_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $response = $this->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $file,
        ]);

        $response->assertRedirect(route('profile.show'));
        $this->assertNotNull($user->fresh()->avatar);
    }

    /**
     * Test that profile update works without avatar (avatar is nullable).
     */
    public function test_profile_update_without_avatar_succeeds(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->put(route('profile.update'), [
            'name' => 'Updated Name',
            'email' => $user->email,
        ]);

        $response->assertRedirect(route('profile.show'));
        $this->assertSame('Updated Name', $user->fresh()->name);
    }
}
