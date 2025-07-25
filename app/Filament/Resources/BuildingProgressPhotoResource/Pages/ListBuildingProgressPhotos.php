<?php

namespace App\Filament\Resources\BuildingProgressPhotoResource\Pages;

use App\Filament\Resources\BuildingProgressPhotoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBuildingProgressPhotos extends ListRecords
{
    protected static string $resource = BuildingProgressPhotoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
