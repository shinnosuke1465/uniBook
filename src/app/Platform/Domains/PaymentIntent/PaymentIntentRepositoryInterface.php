<?php

namespace App\Platform\Domains\PaymentIntent;

use App\Platform\Domains\Textbook\Textbook;
use App\Platform\Domains\User\User;
use App\Platform\Domains\PaymentIntent\PaymentIntent;


interface PaymentIntentRepositoryInterface
{
    public function createPaymentIntent(Textbook $textbook, User $buyer): PaymentIntent;

    public function verifyPaymentIntent(ClientSecret $paymentIntentId): bool;
}
