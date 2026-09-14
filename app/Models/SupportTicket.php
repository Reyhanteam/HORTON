<?php

namespace App\Models;

class SupportTicket extends HortonModel
{
    protected $casts = ['last_replied_at' => 'datetime', 'closed_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(SupportDepartment::class, 'department_id');
    }

    public function messages()
    {
        return $this->hasMany(SupportMessage::class, 'ticket_id');
    }
}
