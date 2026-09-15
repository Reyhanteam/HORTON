<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Notification;
use App\Models\TelegramAccount;

final class NotificationService
{
    public function create(TelegramAccount $account, string $type, string $title, string $body, array $data = []): Notification
    {
        return Notification::query()->create([
            'telegram_account_id' => $account->getKey(),
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);
    }

    public function markRead(Notification $notification): Notification
    {
        $notification->forceFill(['read_at' => now()])->save();
        return $notification->fresh();
    }
}
