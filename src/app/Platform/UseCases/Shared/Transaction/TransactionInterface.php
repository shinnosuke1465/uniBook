<?php

namespace App\Platform\UseCases\Shared\Transaction;

interface TransactionInterface
{
    public function begin(): void;

    public function commit(): void;

    public function rollBack(): void;
}
