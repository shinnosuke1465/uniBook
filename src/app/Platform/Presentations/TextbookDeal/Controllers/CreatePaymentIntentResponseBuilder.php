<?php

declare(strict_types=1);

namespace App\Platform\Presentations\TextbookDeal\Controllers;

use App\Platform\UseCases\TextbookDeal\Dtos\PaymentIntentDto;

readonly class CreatePaymentIntentResponseBuilder
{
    /**
     * PaymentIntentDtoをAPIレスポンス用の配列に変換
     *
     * @param PaymentIntentDto $paymentIntentDto
     * @return array{client_secret: string}
     */
    public static function toArray(PaymentIntentDto $paymentIntentDto): array
    {
        return [
            'client_secret' => $paymentIntentDto->clientSecret,
        ];
    }
}