<?php

namespace App\Models;

class SupportTicket extends HortonModel
{
    protected $casts = ['last_replied_at' => 'datetime', 'closed_at' => 'datetime'];

    public function telegramAccount()
    {
        return $this->belongsTo(TelegramAccount::class, 'telegram_account_id');
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
