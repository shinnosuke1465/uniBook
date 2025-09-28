<?php

declare(strict_types=1);

namespace App\Platform\Domains\PaymentIntent;

use App\Exceptions\DomainException;

readonly class PaymentCurrency
{
    public const JPY = 'jpy';
    public const USD = 'usd';
    public const EUR = 'eur';

    private const SUPPORTED_CURRENCIES = [
        self::JPY,
        self::USD,
        self::EUR,
    ];

    /**
     * @throws DomainException
     */
    public function __construct(
        public string $value,
    ) {
        $this->validate();
    }

    /**
     * @throws DomainException
     */
    private function validate(): void
    {
        if (!in_array(strtolower($this->value), self::SUPPORTED_CURRENCIES, true)) {
            throw new DomainException('Unsupported currency: ' . $this->value);
        }
    }

    public static function jpy(): self
    {
        return new self(self::JPY);
    }

    public static function usd(): self
    {
        return new self(self::USD);
    }

    public static function eur(): self
    {
        return new self(self::EUR);
    }

    public function equals(PaymentCurrency $other): bool
    {
        return strtolower($this->value) === strtolower($other->value);
    }

    public function isJpy(): bool
    {
        return strtolower($this->value) === self::JPY;
    }
}