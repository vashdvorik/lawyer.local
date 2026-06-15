<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class HttpErrorPagesTest extends TestCase
{
    /**
     * Test that 404 page renders the custom error view.
     */
    public function test_404_page_is_rendered(): void
    {
        $response = $this->get('/this-route-does-not-exist');

        $response->assertStatus(404);
    }

    /**
     * Test that health check endpoint is available.
     */
    public function test_health_check_is_available(): void
    {
        $response = $this->get('/up');

        $response->assertStatus(200);
        $response->assertSee('Application up');
    }
}
