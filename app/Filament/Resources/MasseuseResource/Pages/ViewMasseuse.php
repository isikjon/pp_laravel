<?php

namespace App\Filament\Resources\MasseuseResource\Pages;

use App\Filament\Resources\MasseuseResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMasseuse extends ViewRecord
{
    protected static string $resource = MasseuseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

