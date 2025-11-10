<?php

namespace App\Filament\Resources\HomePageSettingsResource\Pages;

use App\Filament\Resources\HomePageSettingsResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageHomePageSettings extends ManageRecords
{
    protected static string $resource = HomePageSettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
