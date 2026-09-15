<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\DomainRuleViolation;
use App\Models\SupportTicket;
use App\Models\TelegramAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class SupportTicketService
{
    public function create(TelegramAccount $account, string $subject, ?int $departmentId = null, string $sensitivity = 'normal'): SupportTicket
    {
        $subject = trim($subject);
        if ($subject === '') {
            throw new DomainRuleViolation('Support ticket subject is required.');
        }

        if (! in_array($sensitivity, ['low', 'normal', 'high'], true)) {
            throw new DomainRuleViolation('Invalid support ticket sensitivity.');
        }

        return DB::transaction(fn (): SupportTicket => SupportTicket::query()->create([
            'uuid' => (string) Str::uuid(),
            'telegram_account_id' => $account->getKey(),
            'department_id' => $departmentId,
            'subject' => $subject,
            'status' => 'open',
            'priority' => 'normal',
            'sensitivity' => $sensitivity,
            'last_message_at' => now(),
        ]));
    }

    public function close(SupportTicket $ticket): SupportTicket
    {
        $ticket->forceFill(['status' => 'closed', 'closed_at' => now()])->save();
        return $ticket->fresh();
    }
}
