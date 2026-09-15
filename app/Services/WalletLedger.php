<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\WalletEntryData;
use App\Exceptions\DomainRuleViolation;
use App\Models\TelegramAccount;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

final class WalletLedger
{
    public function credit(WalletEntryData $entry): WalletTransaction
    {
        if ($entry->amount <= 0) throw new DomainRuleViolation('Wallet credit amount must be positive.');
        return $this->apply($entry, 'credit');
    }

    public function debit(WalletEntryData $entry): WalletTransaction
    {
        if ($entry->amount <= 0) throw new DomainRuleViolation('Wallet debit amount must be positive.');
        return $this->apply($entry, 'debit');
    }

    private function apply(WalletEntryData $entry, string $direction): WalletTransaction
    {
        return DB::transaction(function () use ($entry, $direction): WalletTransaction {
            $idempotencyKey = $entry->metadata['idempotency_key'] ?? null;
            if ($idempotencyKey !== null) {
                $existing = WalletTransaction::query()->where('idempotency_key', $idempotencyKey)->first();
                if ($existing) return $existing;
            }

            TelegramAccount::query()->findOrFail($entry->telegramAccountId);
            $wallet = Wallet::query()->where('telegram_account_id', $entry->telegramAccountId)->lockForUpdate()->first();

            if (! $wallet) {
                $wallet = Wallet::query()->create(['telegram_account_id' => $entry->telegramAccountId, 'balance' => 0]);
                $wallet = Wallet::query()->whereKey($wallet->getKey())->lockForUpdate()->firstOrFail();
            }

            $before = (int) $wallet->balance;
            $after = $direction === 'credit' ? $before + $entry->amount : $before - $entry->amount;
            if ($after < 0) throw new DomainRuleViolation('Insufficient wallet balance.');

            $wallet->forceFill(['balance' => $after])->save();

            return WalletTransaction::query()->create([
                'wallet_id' => $wallet->getKey(),
                'telegram_account_id' => $entry->telegramAccountId,
                'type' => $entry->type,
                'direction' => $direction,
                'amount' => $entry->amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'reference_type' => 'application',
                'description' => $entry->reference,
                'idempotency_key' => $idempotencyKey,
            ]);
        });
    }
}
