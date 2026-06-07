<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful registration.
     */
    public function test_user_can_register_successfully(): void
    {
        Event::fake();

        $response = $this->post('/signup', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    /**
     * Test registration validation errors.
     */
    public function test_registration_validation_errors(): void
    {
        // Duplicate email
        User::factory()->create(['email' => 'john@example.com']);

        $response = $this->post('/signup', [
            'name' => '',
            'email' => 'john@example.com',
            'password' => '123',
            'password_confirmation' => '321',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertCount(1, User::where('email', 'john@example.com')->get());
    }

    /**
     * Test successful login.
     */
    public function test_user_can_login_successfully(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('profile.show'));
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    /**
     * Test login failure with incorrect credentials.
     */
    public function test_user_cannot_login_with_incorrect_credentials(): void
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertFalse(Auth::check());
    }

    /**
     * Test successful logout.
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('home'));
        $this->assertFalse(Auth::check());
    }

    /**
     * Test that guest is redirected to login from verification page.
     */
    public function test_guest_cannot_access_verification_notice(): void
    {
        $response = $this->get(route('verification.notice'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test email verification notice page is accessible.
     */
    public function test_unverified_user_can_access_verification_notice(): void
    {
        $user = User::factory()->unverified()->create();
        $this->actingAs($user);

        $response = $this->get(route('verification.notice'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.verify-email');
    }

    /**
     * Test email verification process.
     */
    public function test_user_can_verify_email(): void
    {
        Event::fake();

        $user = User::factory()->unverified()->create();
        $this->actingAs($user);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->get($verificationUrl);

        $response->assertRedirect(route('profile.show'));
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        Event::assertDispatched(Verified::class);
    }

    /**
     * Test that user can resend verification notification.
     */
    public function test_user_can_resend_verification_notification(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();
        $this->actingAs($user);

        $response = $this->post(route('verification.send'));

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /**
     * Test password reset request page accessibility.
     */
    public function test_forgot_password_page_is_accessible(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.forgot-password');
    }

    /**
     * Test password reset link submission.
     */
    public function test_user_can_request_password_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'john@example.com',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'john@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /**
     * Test password reset form accessibility.
     */
    public function test_password_reset_form_is_accessible(): void
    {
        $response = $this->get(route('password.reset', ['token' => 'some-token']));

        $response->assertStatus(200);
        $response->assertViewIs('auth.reset-password');
    }

    /**
     * Test successful password reset.
     */
    public function test_user_can_reset_password_with_token(): void
    {
        Event::fake();

        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('oldpassword'),
        ]);

        $token = Password::createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'john@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
        Event::assertDispatched(PasswordReset::class);
    }

    /**
     * Test that guest is redirected to admin login page.
     */
    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    /**
     * Test that non-admin user cannot access Filament panel.
     */
    public function test_non_admin_user_cannot_access_filament_panel(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
        ]);
        $this->actingAs($user);

        $response = $this->get('/admin');

        $response->assertStatus(403);
    }

    /**
     * Test that admin user can access Filament panel.
     */
    public function test_admin_user_can_access_filament_panel(): void
    {
        $user = User::factory()->create([
            'is_admin' => true,
        ]);
        $this->actingAs($user);

        $response = $this->get('/admin');

        $response->assertStatus(200);
    }
}
