<?php

namespace App\Filament\Resources\BotSetings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BotSetingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('عنوان تنظیمات')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('نوع')
                    ->badge(),

                TextColumn::make('value')
                    ->label('مقدار')
                    ->formatStateUsing(function ($state, $record): string {
                        if (! $record) {
                            return '';
                        }

                        return match ($record->type) {
                            'boolean' => $state === 'true'
                                ? 'روشن'
                                : 'خاموش',

                            'integer' => (string) $state,

                            'float' => (string) $state,

                            'json' => (string) $state,

                            default => (string) $state,
                        };
                    })
                    ->badge()
                    ->searchable(),

            ])
            ->recordActions([
                EditAction::make()
                    ->label('ویرایش'),
            ])
            ->paginated(false);
    }
}