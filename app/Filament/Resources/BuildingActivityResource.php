<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use DeepCopy\Filter\Filter;
use Illuminate\Validation\Rule;
use App\Models\BuildingActivity;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ForceDeleteAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BuildingActivityResource\Pages;
use App\Filament\Resources\BuildingActivityResource\RelationManagers;

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
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        session()->put('last_building_id', $state);
                    })
                    ->default(function () {
                        return session()->get('last_building_id');
                    }),

                Select::make('activity_id')
                    ->label('Activity')
                    ->relationship('activity', 'name')
                    ->required()
                    ->rules([
                        function ($get) {
                            return Rule::unique('building_activities', 'activity_id')
                                ->where('building_id', $get('building_id'))
                                ->ignore($get('id')); // Use your record's primary key
                        }
                    ]),
                TextInput::make('weightage')
                    ->label('Weightage (%)')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->maxValue(100)
                    ->rules([
                        'required',
                        'numeric',
                        'min:1',
                        'max:100',
                        function (Get $get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                $buildingId = $get('building_id');
                                $currentId = $get('id'); // For edit operations

                                if (!$buildingId) return;

                                $totalWeightage = \App\Models\BuildingActivity::query()
                                    ->where('building_id', $buildingId)
                                    ->when($currentId, fn($query) => $query->where('id', '!=', $currentId))
                                    ->sum('weightage');

                                if (($totalWeightage + $value) > 100) {
                                    $remaining = 100 - $totalWeightage;
                                    $fail("Total weightage for this building cannot exceed 100%. Maximum allowed for this entry: {$remaining}%");
                                }
                            };
                        }
                    ]),
                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->required()
                    ->rules([
                        'required',
                        'numeric',
                        'integer',
                        'min:1',
                        function (Get $get) {
                            return Rule::unique('building_activities', 'sort_order')
                                ->where('building_id', $get('building_id'))
                                ->ignore($get('id')); // For edit operations
                        }
                    ]),
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
            ->modifyQueryUsing(fn(Builder $query) => $query->withTrashed())
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
                SelectFilter::make('building_id')
                    ->label('Building')
                    ->relationship('building', 'name')
                    ->preload()
                    ->searchable()
                    ->indicator('Building'),

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
            'index' => Pages\ListBuildingActivities::route('/'),
            'create' => Pages\CreateBuildingActivity::route('/create'),
            'edit' => Pages\EditBuildingActivity::route('/{record}/edit'),
        ];
    }
}
