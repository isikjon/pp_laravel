<?php

namespace App\Filament\Resources\StripClubResource\Pages;

use App\Filament\Resources\StripClubResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStripClub extends EditRecord
{
    protected static string $resource = StripClubResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

