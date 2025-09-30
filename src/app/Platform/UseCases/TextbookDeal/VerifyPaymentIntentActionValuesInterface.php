<?php

namespace App\Platform\UseCases\TextbookDeal;

use App\Platform\Domains\PaymentIntent\ClientSecret;

interface VerifyPaymentIntentActionValuesInterface
{
    public function getClientSecret(): ClientSecret;
}
