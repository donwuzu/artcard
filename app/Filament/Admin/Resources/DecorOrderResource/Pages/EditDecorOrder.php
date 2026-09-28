<?php

namespace App\Filament\Admin\Resources\DecorOrderResource\Pages;

use App\Filament\Admin\Resources\DecorOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDecorOrder extends EditRecord
{
    protected static string $resource = DecorOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return DecorOrderResource::normalizePaymentData($data);
    }
}
