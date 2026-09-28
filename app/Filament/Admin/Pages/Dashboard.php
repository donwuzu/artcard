<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Artcard Dashboard';

    protected static ?int $navigationSort = 0;
}
