<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\BulkAction;
use App\Models\BuildingScheduleProgress;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BuildingScheduleProgressResource\Pages;
use App\Filament\Resources\BuildingScheduleProgressResource\RelationManagers;

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
                    ->relationship(
                        name: 'building',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) =>
                        $query->whereHas('buildingActivities')
                    )
                    ->required()
                    ->live()
                    ->rules(['required', 'exists:buildings,id']),
                Select::make('activity_id')
                    ->label('Activity')
                    ->options(function (Get $get) {
                        $buildingId = $get('building_id');
                        if (!$buildingId) return [];

                        $availableActivities = \App\Models\BuildingActivity::with('activity')
                            ->where('building_id', $buildingId)
                            ->get()
                            ->pluck('activity.name', 'activity.id');

                        $scheduledActivities = \App\Models\BuildingScheduleProgress::where('building_id', $buildingId)
                            ->when($get('id'), fn($q, $id) => $q->where('id', '!=', $id))
                            ->pluck('activity_id')
                            ->toArray();

                        return $availableActivities->reject(fn($name, $id) => in_array($id, $scheduledActivities));
                    })
                    ->required()
                    ->searchable()
                    ->hint(function (Get $get) {
                        if (!$get('building_id')) return null;

                        $count = \App\Models\BuildingScheduleProgress::where('building_id', $get('building_id'))
                            ->when($get('id'), fn($q, $id) => $q->where('id', '!=', $id))
                            ->count();

                        return "{$count} activities already scheduled for this building";
                    })
                    ->rules([
                        'required',
                        function (Get $get) {
                            return Rule::unique('building_schedule_progress', 'activity_id') // Specify column name
                                ->where('building_id', $get('building_id')) // Simple column reference
                                ->ignore($get('id'));
                        }
                    ])
                    ->live(),
                DatePicker::make('schedule_start_date')
                    ->label('Scheduled Start Date')
                    ->required(),

                DatePicker::make('schedule_completion_date')
                    ->label('Scheduled Completion Date')
                    ->required()
                    ->rules([
                        'required',
                        'date',
                        'after:schedule_start_date'
                    ])
                    ->minDate(fn(Get $get) => $get('schedule_start_date')),

                Textarea::make('notes')
                    ->label('Progress Notes')
                    ->columnSpanFull(),
                Hidden::make('created_by'),
                Hidden::make('updated_by'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->withTrashed())
            ->columns([
                Tables\Columns\TextColumn::make('building.name')
                    ->label('Building')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('activity.name')
                    ->label('Activity')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('schedule_start_date')
                    ->label('Scheduled Start Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('schedule_completion_date')
                    ->label('Scheduled Completion Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->limit(50)
                    ->wrap(),

            ])->filters([
                //
            ])->actions([
                Tables\Actions\ViewAction::make(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                DeleteAction::make(), // regular soft delete
                Action::make('restore')
                    ->label('Restore')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn($record) => !is_null($record->deleted_at))
                    ->action(fn($record) => $record->restore())
                    ->after(fn() => Notification::make()
                        ->title('Record restored')
                        ->success()
                        ->send()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('forceDelete')
                        ->label('Permanently Delete Selected')
                        ->color('danger')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->action(fn(Collection $records) => $records->each->forceDelete()),
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
