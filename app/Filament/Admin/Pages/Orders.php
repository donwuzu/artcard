<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;

class Orders extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.admin.pages.orders';

    protected static ?string $navigationLabel = 'Orders';
}
