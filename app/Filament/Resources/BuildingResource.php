<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Building;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BuildingResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BuildingResource\RelationManagers;

class BuildingResource extends Resource
{
    protected static ?string $model = Building::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('building_no')->required()->unique(table: 'buildings', column: 'building_no', ignoreRecord: true)
                    ->label('Building No'),
                TextInput::make('name')->required()->rules(['required']),
                Select::make('type')
                    ->options([
                        'new' => 'New',
                        'old' => 'Old',
                    ]),
                TextInput::make('location')->required()->rules(['required']),
                TextInput::make('latitude')->required()->rules(['required', 'numeric'])
                    ->numeric(),
                TextInput::make('longitude')->required()->rules(['required', 'numeric'])
                    ->numeric(),

                Select::make('status')
                    ->options([
                        'planned' => 'Planned',
                        'in_progress' => 'In_progress',
                        'completed' => 'Completed',
                        'on_hold' => 'On_hold',

                    ]),
                Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('building_no')->label('Building No'),
                TextColumn::make('name')->label('Building Name'),
                TextColumn::make('location')->label('Location'),
                ToggleColumn::make('is_active')
                    ->label('Is Active')



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
            'index' => Pages\ListBuildings::route('/'),
            'create' => Pages\CreateBuilding::route('/create'),
            'edit' => Pages\EditBuilding::route('/{record}/edit'),
        ];
    }
}
