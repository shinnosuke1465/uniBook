<?php

declare(strict_types=1);

namespace Feature\Platform\Infrastructures\StripeService;

use App\Platform\Domains\PaymentIntent\PaymentIntent;
use App\Platform\Domains\Textbook\Price;
use App\Platform\Infrastructures\StripeService\PaymentIntentMockRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Platform\Domains\Textbook\TestTextbookFactory;
use Tests\Unit\Platform\Domains\User\TestUserFactory;

class PaymentIntentMockRepositoryTest extends TestCase
{
    use DatabaseTransactions;
    private PaymentIntentMockRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PaymentIntentMockRepository();
    }

    public function test_createPaymentIntent_固定のPaymentIntentオブジェクトが返される(): void
    {
        // Arrange
        $textbook = TestTextbookFactory::create(
            price: new Price(2000)
        );
        $buyer = TestUserFactory::create();

        // Act
        $paymentIntent = $this->repository->createPaymentIntent($textbook, $buyer);

        // Assert
        $this->assertInstanceOf(PaymentIntent::class, $paymentIntent);
        $this->assertEquals('pi_123_secret_123', $paymentIntent->getClientSecret());
        $this->assertEquals(2000, $paymentIntent->getAmount());
        $this->assertEquals('jpy', $paymentIntent->getCurrency());
        $this->assertEquals('requires_payment_method', $paymentIntent->getStatus());
    }

    public function test_createPaymentIntent_教科書の価格がamountに反映される(): void
    {
        // Arrange
        $textbook = TestTextbookFactory::create(
            price: new Price(5000)
        );
        $buyer = TestUserFactory::create();

        // Act
        $paymentIntent = $this->repository->createPaymentIntent($textbook, $buyer);

        // Assert
        $this->assertEquals(5000, $paymentIntent->getAmount());
    }

    public function test_createPaymentIntent_異なる価格の教科書でも固定のclient_secretが返される(): void
    {
        // Arrange
        $textbook1 = TestTextbookFactory::create(
            price: new Price(1000)
        );
        $textbook2 = TestTextbookFactory::create(
            price: new Price(3000)
        );
        $buyer = TestUserFactory::create();

        // Act
        $paymentIntent1 = $this->repository->createPaymentIntent($textbook1, $buyer);
        $paymentIntent2 = $this->repository->createPaymentIntent($textbook2, $buyer);

        // Assert
        $this->assertEquals('pi_123_secret_123', $paymentIntent1->getClientSecret());
        $this->assertEquals('pi_123_secret_123', $paymentIntent2->getClientSecret());
        $this->assertEquals(1000, $paymentIntent1->getAmount());
        $this->assertEquals(3000, $paymentIntent2->getAmount());
    }
}
