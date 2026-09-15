<?php

declare(strict_types=1);

namespace App\Filament\Resources\SanaeiServers\Pages;

use App\Filament\Resources\SanaeiServers\SanaeiServerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListSanaeiServers extends ListRecords
{
    protected static string $resource = SanaeiServerResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('افزودن سرور')];
    }
}
