<?php

namespace App\Filament\Admin\Resources\DecorOrderResource\Pages;

use App\Filament\Admin\Resources\DecorOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDecorOrder extends CreateRecord
{
    protected static string $resource = DecorOrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return DecorOrderResource::normalizePaymentData($data);
    }
}
