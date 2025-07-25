<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BuildingProgressPhotoResource\Pages;
use App\Filament\Resources\BuildingProgressPhotoResource\RelationManagers;
use App\Models\BuildingProgressPhoto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BuildingProgressPhotoResource extends Resource
{
    protected static ?string $model = BuildingProgressPhoto::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBuildingProgressPhotos::route('/'),
            'create' => Pages\CreateBuildingProgressPhoto::route('/create'),
            'edit' => Pages\EditBuildingProgressPhoto::route('/{record}/edit'),
        ];
    }
}
