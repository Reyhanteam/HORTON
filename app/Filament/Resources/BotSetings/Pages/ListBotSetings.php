<?php

namespace App\Filament\Resources\BotSetings\Pages;

use App\Filament\Resources\BotSetings\BotSetingResource;
use Filament\Resources\Pages\ListRecords;

class ListBotSetings extends ListRecords
{
    protected static string $resource = BotSetingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}