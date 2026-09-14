<?php

namespace App\Models;

class Product extends HortonModel
{
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function plans()
    {
        return $this->hasMany(Plan::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
