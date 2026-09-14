<?php

namespace App\Filament\Resources\BotSetings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BotSetingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('عنوان تنظیمات')
                    ->disabled(),

                TextInput::make('type')
                    ->label('نوع')
                    ->disabled(),

                Toggle::make('boolean_value')
                    ->label('وضعیت')
                    ->visible(fn ($record): bool => $record?->type === 'boolean')
                    ->default(false),

                TextInput::make('integer_value')
                    ->label('مقدار')
                    ->numeric()
                    ->visible(fn ($record): bool => $record?->type === 'integer'),

                TextInput::make('float_value')
                    ->label('مقدار')
                    ->numeric()
                    ->step(0.01)
                    ->visible(fn ($record): bool => $record?->type === 'float'),

                TextInput::make('string_value')
                    ->label('مقدار')
                    ->visible(fn ($record): bool => $record?->type === 'string'),

                Textarea::make('text_value')
                    ->label('مقدار')
                    ->rows(8)
                    ->visible(fn ($record): bool => $record?->type === 'text'),

                Textarea::make('json_value')
                    ->label('JSON')
                    ->rows(12)
                    ->visible(fn ($record): bool => $record?->type === 'json'),
            ]);
    }
}