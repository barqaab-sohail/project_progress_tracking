<?php

namespace App\Filament\Resources\BuildingScheduleProgressResource\Pages;

use App\Filament\Resources\BuildingScheduleProgressResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBuildingScheduleProgress extends ListRecords
{
    protected static string $resource = BuildingScheduleProgressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
