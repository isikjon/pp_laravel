<?php

namespace App\Filament\Resources\GirlResource\Pages;

use App\Filament\Resources\GirlResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGirl extends ViewRecord
{
    protected static string $resource = GirlResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

