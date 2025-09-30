<?php

declare(strict_types=1);

namespace App\Platform\Domains\PaymentIntent;

use App\Exceptions\DomainException;
use App\Platform\Domains\Shared\Uuid\Uuid;

readonly class PaymentIntentId
{
    private const PREFIX = 'pi_';

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
        if (empty($this->value)) {
            throw new DomainException('PaymentIntentId cannot be empty');
        }

        if (!str_starts_with($this->value, self::PREFIX)) {
            throw new DomainException('Invalid PaymentIntentId format');
        }
    }
}
