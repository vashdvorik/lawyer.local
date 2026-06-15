<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use Filament\Panel;
use Tests\TestCase;

class ProvidersTest extends TestCase
{
    /**
     * Test that panel is configured with correct id and path.
     */
    public function test_admin_panel_provider_configures_panel_correctly(): void
    {
        $provider = new AdminPanelProvider($this->app);
        $panel = $provider->panel(Panel::make());

        $this->assertSame('admin', $panel->getId());
        $this->assertSame('admin', $panel->getPath());
    }

    /**
     * Test that panel is the default.
     */
    public function test_admin_panel_is_default(): void
    {
        $provider = new AdminPanelProvider($this->app);
        $panel = $provider->panel(Panel::make());

        $this->assertTrue($panel->isDefault());
    }

    /**
     * Test that panel has login enabled.
     */
    public function test_admin_panel_has_login_enabled(): void
    {
        $provider = new AdminPanelProvider($this->app);
        $panel = $provider->panel(Panel::make());

        $this->assertTrue($panel->hasLogin());
    }

    /**
     * Test that AppServiceProvider registers and boots without error.
     */
    public function test_app_service_provider_registers_and_boots(): void
    {
        $provider = new AppServiceProvider($this->app);

        // Should not throw any exception
        $provider->register();
        $provider->boot();

        $this->assertTrue(true);
    }
}
