<?php

declare(strict_types=1);

namespace App\Platform\Domains\PaymentIntent;

use App\Exceptions\DomainException;

readonly class ClientSecret
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
            throw new DomainException('Client secret cannot be empty');
        }

        if (!str_starts_with($this->value, self::PREFIX)) {
            throw new DomainException('Invalid client secret format');
        }
    }

    public function equals(ClientSecret $other): bool
    {
        return $this->value === $other->value;
    }
}