<?php

namespace App\Filament\Resources\BuildingActualProgressResource\Pages;

use App\Filament\Resources\BuildingActualProgressResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBuildingActualProgress extends ListRecords
{
    protected static string $resource = BuildingActualProgressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
