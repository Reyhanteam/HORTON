<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\PricingService;
use App\Models\Plan;
use App\Models\TelegramAccount;
use App\Exceptions\DomainRuleViolation;

final class CatalogPricingService implements PricingService
{
    public function priceFor(Plan $plan, ?TelegramAccount $account = null): int
    {
        $price = $plan->prices()
            ->where('is_default', true)
            ->where(function ($query): void {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query): void {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->orderByDesc('id')
            ->first();

        if (! $price) {
            $price = $plan->prices()
                ->where(function ($query): void {
                    $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                })
                ->where(function ($query): void {
                    $query->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                })
                ->orderByDesc('is_default')
                ->orderByDesc('id')
                ->first();
        }

        if (! $price) {
            throw new DomainRuleViolation('No active price is configured for this plan.');
        }

        return max(0, (int) $price->amount);
    }
}
