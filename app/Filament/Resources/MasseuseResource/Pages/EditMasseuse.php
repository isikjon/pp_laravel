<?php

namespace App\Filament\Resources\MasseuseResource\Pages;

use App\Filament\Resources\MasseuseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMasseuse extends EditRecord
{
    protected static string $resource = MasseuseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

