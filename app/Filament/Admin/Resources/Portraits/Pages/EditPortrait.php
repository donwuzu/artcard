<?php

namespace App\Filament\Admin\Resources\Portraits\Pages;

use App\Filament\Admin\Resources\Portraits\PortraitResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPortrait extends EditRecord
{
    protected static string $resource = PortraitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
