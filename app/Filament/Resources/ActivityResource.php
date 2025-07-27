<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Activity;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ActivityResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ActivityResource\RelationManagers;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->unique(table: 'activities', column: 'name', ignoreRecord: true)
                    ->label('Name'),
                TextInput::make('category')->required()->rules(['required']),
                TextInput::make('description')->required()->rules(['required'])->columnSpanFull(),
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
                TextColumn::make('name')->label('Activity Name'),
                TextColumn::make('category'),
                ToggleColumn::make('is_active')
                    ->label('Is Active')

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
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivity::route('/create'),
            'edit' => Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
