<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\TelegramAccount;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class UserRegistered
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly TelegramAccount $account) {}
}
