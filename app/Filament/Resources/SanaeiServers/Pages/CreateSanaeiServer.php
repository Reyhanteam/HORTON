<?php

declare(strict_types=1);

namespace App\Filament\Resources\SanaeiServers\Pages;

use App\Filament\Resources\SanaeiServers\SanaeiServerResource;
use App\Services\Providers\Sanaei\SanaeiServerManager;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

final class CreateSanaeiServer extends CreateRecord
{
    protected static string $resource = SanaeiServerResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['status'] = ($data['enabled'] ?? true) ? 'active' : 'disabled';
        $data['inbound_ids'] = $this->parseInboundIds($data['inbound_ids'] ?? '');

        return app(SanaeiServerManager::class)->create($data);
    }

    private function parseInboundIds(string|array $value): array
    {
        if (is_array($value)) return array_values(array_filter(array_map('intval', $value), fn (int $id): bool => $id > 0));
        return array_values(array_filter(array_map('intval', preg_split('/[,\s]+/', $value, -1, PREG_SPLIT_NO_EMPTY)), fn (int $id): bool => $id > 0));
    }
}
