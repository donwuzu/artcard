<?php

namespace App\Filament\Admin\Resources\Portraits\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PortraitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image_path')
                    ->image()
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
            ]);
    }
}
