<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\StripeService;

use App\Exceptions\DomainException;
use App\Platform\Domains\PaymentIntent\ClientSecret;
use App\Platform\Domains\PaymentIntent\PaymentAmount;
use App\Platform\Domains\PaymentIntent\PaymentCurrency;
use App\Platform\Domains\PaymentIntent\PaymentIntent;
use App\Platform\Domains\PaymentIntent\PaymentIntentId;
use App\Platform\Domains\PaymentIntent\PaymentStatus;
use Stripe\PaymentIntent as StripePaymentIntent;

class PaymentIntentFactory
{
    /**
     *
     * @throws DomainException
     */
    public static function create(StripePaymentIntent $stripePaymentIntent): PaymentIntent
    {
        return new PaymentIntent(
            new PaymentIntentId(),
            new ClientSecret($stripePaymentIntent->client_secret),
            new PaymentAmount($stripePaymentIntent->amount),
            new PaymentCurrency($stripePaymentIntent->currency),
            new PaymentStatus($stripePaymentIntent->status)
        );
    }
}
