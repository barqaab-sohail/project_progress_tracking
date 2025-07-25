<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BuildingScheduleProgressResource\Pages;
use App\Filament\Resources\BuildingScheduleProgressResource\RelationManagers;
use App\Models\BuildingScheduleProgress;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BuildingScheduleProgressResource extends Resource
{
    protected static ?string $model = BuildingScheduleProgress::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('building_id')
                    ->label('Building')
                    ->relationship('building', 'name')
                    ->required()
                    ->rules(['required', 'exists:buildings,id']),
                Select::make('activity_id')
                    ->label('Activity')
                    ->relationship('activity', 'name')
                    ->required()
                    ->rules(['required', 'exists:activities,id']),
                DatePicker::make('scheduled_date')
                    ->label('Scheduled Date')
                    ->required()
                    ->rules(['required', 'date']),
                Textarea::make('notes')
                    ->label('Progress Notes')
                    ->required()
                    ->rules(['required'])
                    ->columnSpanFull(),
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
            'index' => Pages\ListBuildingScheduleProgress::route('/'),
            'create' => Pages\CreateBuildingScheduleProgress::route('/create'),
            'edit' => Pages\EditBuildingScheduleProgress::route('/{record}/edit'),
        ];
    }
}
