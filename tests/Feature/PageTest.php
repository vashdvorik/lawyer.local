<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the home page is accessible.
     */
    public function test_home_page_is_accessible(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.index');
    }

    /**
     * Test that the login page is accessible.
     */
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.login');
    }

    /**
     * Test that the signup page is accessible.
     */
    public function test_signup_page_is_accessible(): void
    {
        $response = $this->get(route('signup'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.signup');
    }
}
