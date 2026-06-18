<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that is_admin = true grants access to Filament panel.
     */
    public function test_can_access_panel_returns_true_for_admin(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $this->assertTrue($user->canAccessPanel(filament()->getPanel('admin')));
    }

    /**
     * Test that is_admin = false denies access to Filament panel.
     */
    public function test_can_access_panel_returns_false_for_non_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->assertFalse($user->canAccessPanel(filament()->getPanel('admin')));
    }

    /**
     * Test that blocked admin cannot access Filament panel.
     */
    public function test_can_access_panel_returns_false_for_blocked_admin(): void
    {
        $user = User::factory()->create([
            'is_admin' => true,
            'blocked_at' => now(),
        ]);

        $this->assertFalse($user->canAccessPanel(filament()->getPanel('admin')));
    }

    /**
     * Test that is_admin is cast to boolean.
     */
    public function test_is_admin_is_cast_to_boolean(): void
    {
        $user = User::factory()->create(['is_admin' => 1]);

        $this->assertTrue($user->is_admin);
        $this->assertIsBool($user->is_admin);
    }

    /**
     * Test that blocked_at is cast to datetime.
     */
    public function test_blocked_at_is_cast_to_datetime(): void
    {
        $user = User::factory()->create(['blocked_at' => '2026-06-18 12:00:00']);

        $this->assertInstanceOf(Carbon::class, $user->blocked_at);
        $this->assertSame('2026-06-18 12:00:00', $user->blocked_at->format('Y-m-d H:i:s'));
    }

    /**
     * Test that blocked helper returns true for blocked user.
     */
    public function test_is_blocked_returns_true_for_blocked_user(): void
    {
        $user = User::factory()->blocked()->create();

        $this->assertTrue($user->isBlocked());
    }

    /**
     * Test that password is automatically hashed.
     */
    public function test_password_is_hashed(): void
    {
        $user = User::factory()->create(['password' => 'plain-text']);

        $this->assertNotEquals('plain-text', $user->password);
        $this->assertTrue(Hash::check('plain-text', $user->password));
    }

    /**
     * Test that email_verified_at is cast to datetime.
     */
    public function test_email_verified_at_is_cast_to_datetime(): void
    {
        $user = User::factory()->create(['email_verified_at' => '2026-01-01 12:00:00']);

        $this->assertInstanceOf(Carbon::class, $user->email_verified_at);
        $this->assertSame('2026-01-01 12:00:00', $user->email_verified_at->format('Y-m-d H:i:s'));
    }

    /**
     * Test that fillable contains expected fields.
     */
    public function test_fillable_contains_expected_fields(): void
    {
        $user = new User;

        $fillable = $user->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('email', $fillable);
        $this->assertContains('password', $fillable);
        $this->assertContains('is_admin', $fillable);
        $this->assertContains('avatar', $fillable);
    }

    /**
     * Test that hidden contains password and remember_token.
     */
    public function test_hidden_contains_password_and_remember_token(): void
    {
        $user = new User;

        $hidden = $user->getHidden();

        $this->assertContains('password', $hidden);
        $this->assertContains('remember_token', $hidden);
    }

    /**
     * Test that user factory creates a valid model.
     */
    public function test_user_factory_creates_valid_model(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->id);
        $this->assertNotEmpty($user->name);
        $this->assertNotEmpty($user->email);
        $this->assertNotNull($user->password);
    }

    /**
     * Test that factory unverified state sets email_verified_at to null.
     */
    public function test_user_factory_unverified_state(): void
    {
        $user = User::factory()->unverified()->create();

        $this->assertNull($user->email_verified_at);
    }

    /**
     * Test that deleting user removes avatar file.
     */
    public function test_user_deleting_removes_avatar_file(): void
    {
        Storage::fake('public');

        Storage::disk('public')->put('avatars/test.jpg', 'avatar-data');

        $user = User::factory()->create([
            'avatar' => 'avatars/test.jpg',
        ]);

        $user->delete();

        Storage::disk('public')->assertMissing('avatars/test.jpg');
    }
}
