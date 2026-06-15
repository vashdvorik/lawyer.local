<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetEdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that reset with invalid token fails.
     */
    public function test_password_reset_with_invalid_token_fails(): void
    {
        User::factory()->create(['email' => 'john@example.com']);

        $response = $this->post(route('password.update'), [
            'token' => 'invalid-token-123456',
            'email' => 'john@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test that reset with expired token fails.
     */
    public function test_password_reset_with_expired_token_fails(): void
    {
        $user = User::factory()->create(['email' => 'john@example.com']);
        $token = Password::createToken($user);

        // Travel 2 hours forward to expire the 1-hour token
        $this->travel(2)->hours();

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'john@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test password reset form validation errors.
     */
    public function test_password_reset_validation_errors(): void
    {
        User::factory()->create(['email' => 'john@example.com']);

        $response = $this->post(route('password.update'), [
            'token' => 'some-token',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertSessionHasErrors(['email', 'password']);
    }

    /**
     * Test that authenticated user is redirected from forgot password page.
     */
    public function test_authenticated_user_is_redirected_from_forgot_password(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('password.request'));

        $response->assertRedirect(route('home'));
    }

    /**
     * Test that email is required when requesting password reset.
     */
    public function test_password_reset_link_requires_email(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => '',
        ]);

        $response->assertSessionHasErrors(['email']);
    }
}
