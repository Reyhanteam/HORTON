<?php

namespace App\Models;

class SupportMessage extends HortonModel
{
    protected $casts = ['attachments' => 'array', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }
}
