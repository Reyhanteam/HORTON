<?php

declare(strict_types=1);

namespace App\Models;

class ProviderPool extends HortonModel
{
    protected $casts = ['rules' => 'array'];

    public function items() { return $this->hasMany(ProviderPoolItem::class); }
}
