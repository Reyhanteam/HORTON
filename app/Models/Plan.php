<?php

declare(strict_types=1);

namespace App\Models;

class Plan extends HortonModel
{
    protected $casts = ['duration_value' => 'integer', 'capacity_value' => 'integer', 'limits' => 'array', 'metadata' => 'array', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'is_trial' => 'boolean'];

    public function getDurationAttribute(): int
    {
        return (int) ($this->attributes['duration_value'] ?? 0);
    }

    public function getCapacityAttribute(): int
    {
        return (int) ($this->attributes['capacity_value'] ?? 0);
    }

    public function product() { return $this->belongsTo(Product::class); }
    public function prices() { return $this->hasMany(PlanPrice::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }
}
