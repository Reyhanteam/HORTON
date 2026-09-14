<?php

namespace App\Filament\Resources\BotSetings\Pages;

use App\Filament\Resources\BotSetings\BotSetingResource;
use Filament\Resources\Pages\EditRecord;

class EditBotSeting extends EditRecord
{
    protected static string $resource = BotSetingResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = match ($this->record->type) {
            'boolean' => array_merge($data, [
                'boolean_value' => $this->record->value === 'true',
            ]),

            'integer' => array_merge($data, [
                'integer_value' => (int) $this->record->value,
            ]),

            'float' => array_merge($data, [
                'float_value' => (float) $this->record->value,
            ]),

            'string' => array_merge($data, [
                'string_value' => $this->record->value,
            ]),

            'text' => array_merge($data, [
                'text_value' => $this->record->value,
            ]),

            'json' => array_merge($data, [
                'json_value' => $this->record->value,
            ]),

            default => $data,
        };

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['value'] = match ($this->record->type) {
            'boolean' => !empty($data['boolean_value']) ? 'true' : 'false',

            'integer' => (string) $data['integer_value'],

            'float' => (string) $data['float_value'],

            'string' => (string) $data['string_value'],

            'text' => (string) $data['text_value'],

            'json' => (string) $data['json_value'],

            default => $this->record->value,
        };

        unset(
            $data['boolean_value'],
            $data['integer_value'],
            $data['float_value'],
            $data['string_value'],
            $data['text_value'],
            $data['json_value'],
        );

        return $data;
    }
}