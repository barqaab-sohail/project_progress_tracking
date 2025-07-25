<?php

namespace App\Filament\Resources\BuildingActivityResource\Pages;

use App\Filament\Resources\BuildingActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBuildingActivities extends ListRecords
{
    protected static string $resource = BuildingActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
