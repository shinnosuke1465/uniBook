<?php

declare(strict_types=1);

namespace App\Packages\Infrastructures\Shared\Transaction;

use App\Platform\UseCases\Shared\Transaction\TransactionInterface;

class MockTransaction implements TransactionInterface
{
    public function begin(): void
    {
    }

    public function commit(): void
    {
    }

    public function rollBack(): void
    {
    }
}
