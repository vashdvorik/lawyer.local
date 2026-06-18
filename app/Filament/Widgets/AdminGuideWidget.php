<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AdminGuideWidget extends Widget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = -2;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.widgets.admin-guide-widget';
}
