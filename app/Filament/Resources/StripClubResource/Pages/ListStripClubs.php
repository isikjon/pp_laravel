<?php

namespace App\Filament\Resources\StripClubResource\Pages;

use App\Filament\Resources\StripClubResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStripClubs extends ListRecords
{
    protected static string $resource = StripClubResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

