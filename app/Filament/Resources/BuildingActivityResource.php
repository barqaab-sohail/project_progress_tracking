<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BuildingActivityResource\Pages;
use App\Filament\Resources\BuildingActivityResource\RelationManagers;
use App\Models\BuildingActivity;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BuildingActivityResource extends Resource
{
    protected static ?string $model = BuildingActivity::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('building_id')
                    ->label('Building')
                    ->relationship('building', 'name')
                    ->required()
                    ->rules(['required']),
                Select::make('activity_id')
                    ->label('Activity')
                    ->relationship('activity', 'name')
                    ->required()
                    ->rules(['required']),
                TextInput::make('weightage')
                    ->label('Weightage')
                    ->numeric()
                    ->required()
                    ->rules(['required', 'numeric']),
                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->required()
                    ->rules(['required', 'numeric']),
                Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true)
                    ->rules(['boolean'])
                    ->inline(false),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('building.name')
                    ->label('Building'),
                TextColumn::make('activity.name')
                    ->label('Activity'),
                TextColumn::make('weightage')
                    ->label('Weightage'),
                TextColumn::make('sort_order')
                    ->label('Sort Order'),
                ToggleColumn::make('is_active')
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
            'index' => Pages\ListBuildingActivities::route('/'),
            'create' => Pages\CreateBuildingActivity::route('/create'),
            'edit' => Pages\EditBuildingActivity::route('/{record}/edit'),
        ];
    }
}
