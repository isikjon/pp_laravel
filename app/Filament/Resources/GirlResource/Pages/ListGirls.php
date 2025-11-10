<?php

namespace App\Filament\Resources\GirlResource\Pages;

use App\Filament\Resources\GirlResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGirls extends ListRecords
{
    protected static string $resource = GirlResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

