<?php

namespace App\Models;

class GiftCodeRedemption extends HortonModel
{
    protected $casts = ['value' => 'integer'];

    public function giftCode()
    {
        return $this->belongsTo(GiftCode::class);
    }

    public function telegramAccount()
    {
        return $this->belongsTo(TelegramAccount::class, 'telegram_account_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
