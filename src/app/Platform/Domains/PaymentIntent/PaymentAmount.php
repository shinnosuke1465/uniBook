<?php

declare(strict_types=1);

namespace App\Platform\Domains\PaymentIntent;

use App\Exceptions\DomainException;

readonly class PaymentAmount
{
    /**
     * @throws DomainException
     */
    public function __construct(
        public int $value,
    ) {
        $this->validate();
    }

    /**
     * @throws DomainException
     */
    private function validate(): void
    {
        if ($this->value <= 0) {
            throw new DomainException('Payment amount must be greater than 0');
        }

        // Stripeの最小金額（50円）をチェック
        if ($this->value < 50) {
            throw new DomainException('Payment amount must be at least 50 yen');
        }

        // Stripeの最大金額（999万円）をチェック
        if ($this->value > 9999999) {
            throw new DomainException('Payment amount must be less than 9,999,999 yen');
        }
    }

    public function equals(PaymentAmount $other): bool
    {
        return $this->value === $other->value;
    }

    public function toYen(): string
    {
        return number_format($this->value) . '円';
    }
}