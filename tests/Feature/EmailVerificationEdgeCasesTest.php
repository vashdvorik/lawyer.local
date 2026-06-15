<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationEdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that verified user still sees the verification notice page.
     * The controller does not auto-redirect verified users from this page.
     */
    public function test_verified_user_can_still_access_verification_notice(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($user);

        $response = $this->get(route('verification.notice'));

        $response->assertStatus(200);
    }

    /**
     * Test that verified user cannot resend verification notification.
     */
    public function test_verified_user_cannot_resend_verification(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($user);

        $response = $this->post(route('verification.send'));

        $response->assertRedirect(route('home'));
    }

    /**
     * Test that verification with invalid hash fails.
     */
    public function test_verification_with_invalid_hash_fails(): void
    {
        $user = User::factory()->unverified()->create();
        $this->actingAs($user);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->getKey(), 'hash' => sha1('wrong-email@example.com')]
        );

        $response = $this->get($verificationUrl);

        // With invalid hash, Laravel redirects with an error or shows 403
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    /**
     * Test that already verified email is handled gracefully on re-verification.
     */
    public function test_already_verified_email_shows_message(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($user);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->get($verificationUrl);

        $response->assertRedirect(route('profile.show'));
    }
}
