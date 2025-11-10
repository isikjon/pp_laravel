<?php

namespace App\Filament\Resources\StripClubResource\Pages;

use App\Filament\Resources\StripClubResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStripClub extends ViewRecord
{
    protected static string $resource = StripClubResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

