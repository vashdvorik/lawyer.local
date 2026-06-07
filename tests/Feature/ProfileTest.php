<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guest cannot access profile.
     */
    public function test_guest_cannot_access_profile(): void
    {
        $response = $this->get(route('profile.show'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that authenticated user can access profile.
     */
    public function test_authenticated_user_can_access_profile(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('profile.show'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.profile');
        $response->assertViewHas('user', $user);
    }

    /**
     * Test that guest cannot access profile edit page.
     */
    public function test_guest_cannot_access_profile_edit(): void
    {
        $response = $this->get(route('profile.edit'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that authenticated user can access profile edit page.
     */
    public function test_authenticated_user_can_access_profile_edit(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.profile-edit');
        $response->assertViewHas('user', $user);
    }

    /**
     * Test that user can update profile details.
     */
    public function test_user_can_update_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);
        $this->actingAs($user);

        $response = $this->put(route('profile.update'), [
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);

        $response->assertRedirect(route('profile.show'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);
    }

    /**
     * Test validation for profile update.
     */
    public function test_profile_update_validation(): void
    {
        $otherUser = User::factory()->create(['email' => 'other@example.com']);
        $user = User::factory()->create([
            'name' => 'My Name',
            'email' => 'my@example.com',
        ]);
        $this->actingAs($user);

        $response = $this->put(route('profile.update'), [
            'name' => '',
            'email' => 'other@example.com', // Duplicate email
        ]);

        $response->assertSessionHasErrors(['name', 'email']);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'My Name',
            'email' => 'my@example.com',
        ]);
    }

    /**
     * Test that user can update password.
     */
    public function test_user_can_update_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123'),
        ]);
        $this->actingAs($user);

        $response = $this->put(route('profile.password'), [
            'current_password' => 'oldpassword123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('profile.show'));
        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    /**
     * Test password update validation.
     */
    public function test_password_update_validation(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword123'),
        ]);
        $this->actingAs($user);

        $response = $this->put(route('profile.password'), [
            'current_password' => 'wrongpassword',
            'password' => 'new',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertSessionHasErrors(['current_password', 'password']);
        $this->assertTrue(Hash::check('oldpassword123', $user->fresh()->password));
    }

    /**
     * Test that user can upload avatar.
     */
    public function test_user_can_upload_avatar(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $file = \Illuminate\Http\UploadedFile::fake()->image('avatar.jpg', 600, 600);

        $response = $this->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $file,
        ]);

        $response->assertRedirect(route('profile.show'));
        $user = $user->fresh();
        
        $this->assertNotNull($user->avatar);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'avatar' => $user->avatar,
        ]);

        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($user->avatar);
    }

    /**
     * Test that old avatar is deleted when new avatar is uploaded.
     */
    public function test_old_avatar_is_deleted_when_new_avatar_is_uploaded(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        // First upload
        $oldFile = \Illuminate\Http\UploadedFile::fake()->image('old_avatar.jpg');
        $oldPath = $oldFile->store('avatars', 'public');
        $user->update(['avatar' => $oldPath]);

        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($oldPath);

        // Second upload
        $newFile = \Illuminate\Http\UploadedFile::fake()->image('new_avatar.jpg');
        $response = $this->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $newFile,
        ]);

        $response->assertRedirect(route('profile.show'));
        $user = $user->fresh();

        $this->assertNotEquals($oldPath, $user->avatar);
        \Illuminate\Support\Facades\Storage::disk('public')->assertMissing($oldPath);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($user->avatar);
    }
}
