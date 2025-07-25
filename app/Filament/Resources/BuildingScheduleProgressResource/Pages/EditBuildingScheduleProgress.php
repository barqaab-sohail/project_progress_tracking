<?php

namespace App\Filament\Resources\BuildingScheduleProgressResource\Pages;

use App\Filament\Resources\BuildingScheduleProgressResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBuildingScheduleProgress extends EditRecord
{
    protected static string $resource = BuildingScheduleProgressResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
