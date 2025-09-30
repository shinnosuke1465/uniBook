<?php

namespace App\Platform\UseCases\TextbookDeal;

use App\Platform\Domains\PaymentIntent\PaymentIntentId;

interface VerifyPaymentIntentActionValuesInterface
{
    public function getPaymentIntentId(): PaymentIntentId;
}
