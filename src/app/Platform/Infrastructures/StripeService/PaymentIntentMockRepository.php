<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\StripeService;

use App\Platform\Domains\PaymentIntent\PaymentIntentId;
use App\Platform\Domains\PaymentIntent\PaymentIntentRepositoryInterface;
use App\Platform\Domains\Textbook\Textbook;
use App\Platform\Domains\User\User;
use App\Platform\Domains\PaymentIntent\PaymentIntent;
use Stripe\PaymentIntent as ServicePaymentIntent;

readonly class PaymentIntentMockRepository implements PaymentIntentRepositoryInterface
{
    public function createPaymentIntent(Textbook $textbook, User $buyer): PaymentIntent
    {
        // PaymentIntentをモックするために固定の値を返す
        $servicePaymentIntent = (new ServicePaymentIntent())::constructFrom([
            'id' => 'pi_123',
            'amount' => $textbook->price->value,
            'currency' => 'jpy',
            'status' => 'requires_payment_method',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
            'client_secret' => 'pi_123_secret_123',
        ]);

        return PaymentIntentFactory::create($servicePaymentIntent);
    }

    public function verifyPaymentIntent(PaymentIntentId $paymentIntentId): bool
    {
        // モックでは常にtrueを返す
        return true;
    }
}
