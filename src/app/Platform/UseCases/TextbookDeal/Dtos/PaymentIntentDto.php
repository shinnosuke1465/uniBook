<?php

declare(strict_types=1);

namespace App\Platform\UseCases\TextbookDeal\Dtos;

use App\Platform\Domains\PaymentIntent\PaymentIntent;

readonly class PaymentIntentDto
{
    public function __construct(
        public string $id,
        public string $clientSecret,
        public int $amount,
        public string $currency,
        public string $status,
    ) {
    }

    /**
     * PaymentIntentドメインモデルからDTOを生成
     */
    public static function create(PaymentIntent $paymentIntent): self
    {
        return new self(
            $paymentIntent->id->value,
            $paymentIntent->clientSecret->value,
            $paymentIntent->amount->value,
            $paymentIntent->currency->value,
            $paymentIntent->status->value,
        );
    }
}
