<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\Contracts\PaymentGateway;
use App\DTOs\PaymentRequest;
use App\DTOs\PaymentResult;

final class FakePaymentGateway implements PaymentGateway
{
    public function pay(PaymentRequest $request): PaymentResult
    {
        return new PaymentResult('pending', 'fake-'.$request->orderId, null, ['gateway' => 'fake']);
    }

    public function verify(PaymentRequest $request, string $authority): PaymentResult
    {
        return new PaymentResult('success', $authority, 'fake-ref-'.$request->orderId, ['gateway' => 'fake']);
    }
}
