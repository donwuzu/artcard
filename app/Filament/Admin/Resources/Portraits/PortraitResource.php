<?php

namespace App\Filament\Admin\Resources\Portraits;

use App\Models\Portrait;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PortraitResource extends Resource
{
    protected static ?string $model = Portrait::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static string|\UnitEnum|null $navigationGroup = 'Catalog';

    protected static ?string $navigationLabel = 'Portraits';

    protected static ?string $modelLabel = 'Portrait';

    protected static ?string $pluralModelLabel = 'Portraits';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Portrait Details')
                    ->schema([

                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),

                        FileUpload::make('image_path')
                            ->label('Portrait Image')
                            ->image()
                            ->disk('public')
                            ->directory('portraits')
                            ->required(),

                        TextInput::make('price')
                            ->numeric()
                            ->prefix('KES')
                            ->required(),

                        Toggle::make('is_active')
                            ->default(true),

                        Toggle::make('is_featured')
                            ->default(false),

                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                ImageColumn::make('image_path')
                    ->disk('public'),

                TextColumn::make('title')
                    ->searchable(),

                TextColumn::make('price')
                    ->money('KES'),

                IconColumn::make('is_active')
                    ->boolean(),

                IconColumn::make('is_featured')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->since(),

            ])

            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])

            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPortraits::route('/'),
            'create' => Pages\CreatePortrait::route('/create'),
            'edit' => Pages\EditPortrait::route('/{record}/edit'),
        ];
    }
}
