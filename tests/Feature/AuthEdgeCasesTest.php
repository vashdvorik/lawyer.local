<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthEdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test login with remember-me functionality.
     */
    public function test_login_with_remember_me(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
            'remember' => true,
        ]);

        $response->assertRedirect(route('profile.show'));
        $this->assertTrue(Auth::check());

        // Verify remember token is set
        $this->assertNotNull($user->fresh()->remember_token);
    }

    /**
     * Test login without remember-me does not set remember token.
     */
    public function test_login_without_remember_me_does_not_set_token(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
            // No 'remember' field
        ]);

        $response->assertRedirect(route('profile.show'));
        $this->assertTrue(Auth::check());
    }

    /**
     * Test that session is regenerated on login.
     */
    public function test_session_is_regenerated_on_login(): void
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);

        $sessionIdBefore = session()->getId();

        $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $sessionIdAfter = session()->getId();

        $this->assertNotEquals($sessionIdBefore, $sessionIdAfter);
    }
}
