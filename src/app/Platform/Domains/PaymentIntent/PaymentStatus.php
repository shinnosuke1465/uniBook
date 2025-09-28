<?php

declare(strict_types=1);

namespace App\Platform\Domains\PaymentIntent;

use App\Exceptions\DomainException;

readonly class PaymentStatus
{
    public const REQUIRES_PAYMENT_METHOD = 'requires_payment_method';
    public const REQUIRES_CONFIRMATION = 'requires_confirmation';
    public const REQUIRES_ACTION = 'requires_action';
    public const PROCESSING = 'processing';
    public const REQUIRES_CAPTURE = 'requires_capture';
    public const CANCELED = 'canceled';
    public const SUCCEEDED = 'succeeded';

    private const VALID_STATUSES = [
        self::REQUIRES_PAYMENT_METHOD,
        self::REQUIRES_CONFIRMATION,
        self::REQUIRES_ACTION,
        self::PROCESSING,
        self::REQUIRES_CAPTURE,
        self::CANCELED,
        self::SUCCEEDED,
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
        if (!in_array($this->value, self::VALID_STATUSES, true)) {
            throw new DomainException('Invalid payment status: ' . $this->value);
        }
    }

    public static function requiresPaymentMethod(): self
    {
        return new self(self::REQUIRES_PAYMENT_METHOD);
    }

    public static function requiresConfirmation(): self
    {
        return new self(self::REQUIRES_CONFIRMATION);
    }

    public static function requiresAction(): self
    {
        return new self(self::REQUIRES_ACTION);
    }

    public static function processing(): self
    {
        return new self(self::PROCESSING);
    }

    public static function requiresCapture(): self
    {
        return new self(self::REQUIRES_CAPTURE);
    }

    public static function canceled(): self
    {
        return new self(self::CANCELED);
    }

    public static function succeeded(): self
    {
        return new self(self::SUCCEEDED);
    }

    public function equals(PaymentStatus $other): bool
    {
        return $this->value === $other->value;
    }

    public function isSucceeded(): bool
    {
        return $this->value === self::SUCCEEDED;
    }

    public function isCanceled(): bool
    {
        return $this->value === self::CANCELED;
    }

}
