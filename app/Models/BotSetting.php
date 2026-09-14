<?php

namespace App\Models;

class BotSetting extends HortonModel
{
    protected $table = 'bot_settings';

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function isBoolean(): bool
    {
        return $this->type === 'boolean';
    }

    public function isInteger(): bool
    {
        return $this->type === 'integer';
    }

    public function isFloat(): bool
    {
        return $this->type === 'float';
    }

    public function isString(): bool
    {
        return $this->type === 'string';
    }

    public function isText(): bool
    {
        return $this->type === 'text';
    }

    public function isJson(): bool
    {
        return $this->type === 'json';
    }

    public function typedValue(): mixed
    {
        return match ($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->value,
            'float' => (float) $this->value,
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }
}