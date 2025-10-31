<?php

namespace App\Filament\Resources\GirlResource\Pages;

use App\Filament\Resources\GirlResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGirl extends EditRecord
{
    protected static string $resource = GirlResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
