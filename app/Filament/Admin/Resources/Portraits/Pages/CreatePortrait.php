<?php

namespace App\Filament\Admin\Resources\Portraits\Pages;

use App\Filament\Admin\Resources\Portraits\PortraitResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePortrait extends CreateRecord
{
    protected static string $resource = PortraitResource::class;
}
