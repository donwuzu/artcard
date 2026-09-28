<?php

namespace App\Filament\Admin\Resources\DecorOrderResource\Pages;

use App\Filament\Admin\Resources\DecorOrderResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDecorOrder extends ViewRecord
{
    protected static string $resource = DecorOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
