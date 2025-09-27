<?php

declare(strict_types=1);

namespace App\Platform\Domains\PaymentIntent;

use App\Platform\Domains\Shared\String\String255;

readonly class PaymentIntent
{
    public function __construct(
        public PaymentIntentId $id,
        public ClientSecret $clientSecret,
        public PaymentAmount $amount,
        public PaymentCurrency $currency,
        public PaymentStatus $status,
    ) {
    }

    public static function create(
        ClientSecret $clientSecret,
        PaymentAmount $amount,
        PaymentCurrency $currency,
        PaymentStatus $status,
    ): self {
        return new self(
            new PaymentIntentId(),
            $clientSecret,
            $amount,
            $currency,
            $status,
        );
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret->value;
    }

    public function getAmount(): int
    {
        return $this->amount->value;
    }

    public function getCurrency(): string
    {
        return $this->currency->value;
    }

    public function getStatus(): string
    {
        return $this->status->value;
    }
}
