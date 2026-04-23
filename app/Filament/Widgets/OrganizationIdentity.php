<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class OrganizationIdentity extends Widget
{
    protected string $view = 'filament.widgets.organization-identity';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;
}

