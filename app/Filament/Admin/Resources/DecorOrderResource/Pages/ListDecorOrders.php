<?php

namespace App\Filament\Admin\Resources\DecorOrderResource\Pages;

use App\Filament\Admin\Resources\DecorOrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDecorOrders extends ListRecords
{
    protected static string $resource = DecorOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
