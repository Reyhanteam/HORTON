<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOs\PaymentRequest;
use App\DTOs\PaymentResult;

interface PaymentGateway
{
    public function pay(PaymentRequest $request): PaymentResult;
    public function verify(PaymentRequest $request, string $authority): PaymentResult;
}
