<?php

namespace App\Filament\Admin\Resources\Portraits\Pages;

use App\Filament\Admin\Resources\Portraits\PortraitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPortraits extends ListRecords
{
    protected static string $resource = PortraitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
