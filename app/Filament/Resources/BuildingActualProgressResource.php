<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use App\Models\BuildingActualProgress;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BuildingActualProgressResource\Pages;
use App\Filament\Resources\BuildingActualProgressResource\RelationManagers;

class BuildingActualProgressResource extends Resource
{
    protected static ?string $model = BuildingActualProgress::class;

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
                        modifyQueryUsing: fn(Builder $query) => $query->whereHas('buildingActivities')
                    )
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set) {
                        $set('activity_id', null);
                        $set('progress_percentage', null);
                        $set('progress_date', null);
                        $set('latest_progress', null);
                    })
                    ->rules(['required', 'exists:buildings,id']),

                Select::make('activity_id')
                    ->label('Activity')
                    ->options(function (Get $get) {
                        $buildingId = $get('building_id');
                        return $buildingId
                            ? \App\Models\BuildingActivity::where('building_id', $buildingId)
                            ->with('activity')
                            ->get()
                            ->pluck('activity.name', 'activity.id')
                            : [];
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $buildingId = $get('building_id');
                        $activityId = $get('activity_id');

                        if ($buildingId && $activityId) {
                            $latestProgress = BuildingActualProgress::where('building_id', $buildingId)
                                ->where('activity_id', $activityId)
                                ->latest('progress_date')
                                ->first();

                            if ($latestProgress) {
                                $set('progress_percentage', $latestProgress->progress_percentage);
                                $set('progress_date', $latestProgress->progress_date->format('Y-m-d')); // Ensure proper date format
                                $set('latest_progress', $latestProgress->progress_percentage);
                            } else {
                                $set('progress_percentage', 0);
                                $set('progress_date', now()->format('Y-m-d')); // Default to today if no progress exists
                                $set('latest_progress', null);
                            }
                        }
                    })
                    ->rules(['required', 'exists:activities,id']),

                TextInput::make('progress_percentage')
                    ->label('Progress Percentage')
                    ->numeric()
                    ->required()
                    ->rules([
                        'required',
                        'numeric',
                        'min:0',
                        'max:100',
                        function (Get $get) {
                            return function (string $attribute, $value, Closure $fail) use ($get) {
                                if ($get('../../../edit_mode')) {
                                    return;
                                }

                                $latestProgress = $get('latest_progress');
                                if ($latestProgress !== null && $value <= $latestProgress) {
                                    $fail("Progress percentage must be greater than the last recorded value ($latestProgress%).");
                                }
                            };
                        },
                    ]),

                DatePicker::make('progress_date')
                    ->label('Progress Date')
                    ->required()
                    ->rules([
                        'required',
                        'date',
                        function (Get $get) {
                            return function (string $attribute, $value, Closure $fail) use ($get) {
                                if ($get('../../../edit_mode')) {
                                    return;
                                }

                                $latestProgressDate = BuildingActualProgress::where('building_id', $get('building_id'))
                                    ->where('activity_id', $get('activity_id'))
                                    ->latest('progress_date')
                                    ->value('progress_date');

                                if ($latestProgressDate && $value <= $latestProgressDate) {
                                    $fail("Progress date must be after the last recorded date ($latestProgressDate).");
                                }
                            };
                        },
                    ]),

                Textarea::make('notes')
                    ->label('Notes'),

                Hidden::make('latest_progress'),
                Hidden::make('edit_mode')
                    ->default(fn($operation) => $operation === 'edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->withTrashed())
            ->columns([
                TextColumn::make('building.name')->label('Building Name'),
                TextColumn::make('activity.name')->label('Activity Name'),
                TextColumn::make('progress_percentage')->label('Progress Percentage'),
                TextColumn::make('progress_date')->date()->label('Progress Date'),
                TextColumn::make('notes')->limit(50)->label('Notes'),
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
            'index' => Pages\ListBuildingActualProgress::route('/'),
            'create' => Pages\CreateBuildingActualProgress::route('/create'),
            'edit' => Pages\EditBuildingActualProgress::route('/{record}/edit'),
        ];
    }
}
