<?php

namespace App\Filament\Resources\BotSetings;

use App\Filament\Resources\BotSetings\Pages\EditBotSeting;
use App\Filament\Resources\BotSetings\Pages\ListBotSetings;
use App\Filament\Resources\BotSetings\Schemas\BotSetingForm;
use App\Filament\Resources\BotSetings\Tables\BotSetingsTable;
use App\Models\BotSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BotSetingResource extends Resource
{
    protected static ?string $model = BotSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'تنظیمات بات تلگرام';

    protected static ?string $modelLabel = 'تنظیم بات';

    protected static ?string $pluralModelLabel = 'تنظیمات بات تلگرام';

    protected static string|\UnitEnum|null $navigationGroup = 'تنظیمات';

    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return BotSetingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BotSetingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBotSetings::route('/'),
            'edit' => EditBotSeting::route('/{record}/edit'),
        ];
    }
}
