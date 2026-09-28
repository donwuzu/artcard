<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;

class DecorOrders extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationLabel = 'Decor Orders';

    protected string $view = 'filament.admin.pages.decor-orders';
}
