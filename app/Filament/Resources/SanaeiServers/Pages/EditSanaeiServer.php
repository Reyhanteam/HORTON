<?php

declare(strict_types=1);

namespace App\Filament\Resources\SanaeiServers\Pages;

use App\Filament\Resources\SanaeiServers\SanaeiServerResource;
use App\Models\ServiceProviderAccount;
use App\Services\Providers\Sanaei\SanaeiServerManager;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

final class EditSanaeiServer extends EditRecord
{
    protected static string $resource = SanaeiServerResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var ServiceProviderAccount $record */
        $record = $this->record;
        $credentials = $record->secureCredentials();
        $metadata = $record->metadata ?? [];

        $data['base_url'] = $credentials['base_url'] ?? '';
        $data['inbound_ids'] = implode(',', $metadata['inbound_ids'] ?? []);
        $data['region'] = $metadata['region'] ?? '';
        $data['notes'] = $metadata['notes'] ?? '';
        $data['enabled'] = ($record->status ?? 'active') === 'active';
        $data['timeout'] = $credentials['timeout'] ?? 15;
        $data['connect_timeout'] = $credentials['connect_timeout'] ?? 5;
        $data['retry_times'] = $credentials['retry_times'] ?? 2;
        $data['token'] = '';

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['status'] = ($data['enabled'] ?? true) ? 'active' : 'disabled';
        $data['inbound_ids'] = $this->parseInboundIds($data['inbound_ids'] ?? '');

        return app(SanaeiServerManager::class)->update($record, $data);
    }

    private function parseInboundIds(string|array $value): array
    {
        if (is_array($value)) return array_values(array_filter(array_map('intval', $value), fn (int $id): bool => $id > 0));
        return array_values(array_filter(array_map('intval', preg_split('/[,\s]+/', $value, -1, PREG_SPLIT_NO_EMPTY)), fn (int $id): bool => $id > 0));
    }
}
