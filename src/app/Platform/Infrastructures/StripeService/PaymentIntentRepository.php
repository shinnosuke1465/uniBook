<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\StripeService;

use App\Exceptions\DomainException;
use App\Exceptions\RepositoryException;
use App\Platform\Domains\PaymentIntent\PaymentIntent;
use App\Platform\Domains\PaymentIntent\PaymentIntentRepositoryInterface;
use App\Platform\Domains\Textbook\Textbook;
use App\Platform\Domains\User\User;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent as StripePaymentIntent;
use Stripe\Stripe;

readonly class PaymentIntentRepository implements PaymentIntentRepositoryInterface
{
    /**
     * @throws DomainException
     * @throws ApiErrorException
     */
    public function createPaymentIntent(Textbook $textbook, User $buyer): PaymentIntent
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $stripePaymentIntent = StripePaymentIntent::create([
            'amount' => $textbook->price->value,
            'currency' => 'jpy',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);

        // ファクトリを使用してStripeレスポンスをドメインモデルに変換
        return PaymentIntentFactory::create($stripePaymentIntent);
    }
}
