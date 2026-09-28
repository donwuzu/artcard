<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?string $navigationLabel = 'Orders';

    protected static ?string $modelLabel = 'Portrait Order';

    protected static ?string $pluralModelLabel = 'Portrait Orders';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(255),

                        TextInput::make('location')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(3),

                Section::make('Order')
                    ->schema([
                        KeyValue::make('items')
                            ->keyLabel('Item ID')
                            ->valueLabel('Quantity')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('total_price')
                            ->numeric()
                            ->prefix(fn ($get) => static::currencySymbol($get('currency') ?: 'UGX'))
                            ->required(),

                        Select::make('currency')
                            ->options(static::currencyOptions())
                            ->default('UGX')
                            ->required(),

                        Select::make('status')
                            ->label('Payment Status')
                            ->options([
                                'unpaid' => 'Unpaid',
                                'paid' => 'Paid',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('unpaid')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->searchable(),

                TextColumn::make('location')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('total_price')
                    ->label('Total')
                    ->formatStateUsing(fn ($state, Model $record): string => static::formatMoney($state, $record->currency))
                    ->sortable(),

                TextColumn::make('currency')
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Payment')
                    ->badge()
                    ->colors([
                        'warning' => 'unpaid',
                        'success' => 'paid',
                        'danger' => 'cancelled',
                    ]),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ]),

                SelectFilter::make('is_paid')
                    ->label('Payment')
                    ->options([
                        0 => 'Unpaid',
                        1 => 'Paid',
                    ]),

                SelectFilter::make('currency')
                    ->options(static::currencyOptions()),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function normalizePaymentData(array $data): array
    {
        $data['currency'] = $data['currency'] ?? 'UGX';
        $data['status'] = $data['status'] ?? 'unpaid';
        $data['is_paid'] = $data['status'] === 'paid';

        return $data;
    }

    protected static function currencyOptions(): array
    {
        return collect(config('pricing.currencies', []))
            ->mapWithKeys(fn (array $settings, string $currency): array => [
                $currency => "{$currency} ({$settings['symbol']})",
            ])
            ->all();
    }

    protected static function currencySymbol(?string $currency): string
    {
        return config("pricing.currencies.{$currency}.symbol", $currency ?: 'UGX');
    }

    protected static function formatMoney(mixed $amount, ?string $currency): string
    {
        return static::currencySymbol($currency).' '.number_format((float) $amount, 2);
    }
}
