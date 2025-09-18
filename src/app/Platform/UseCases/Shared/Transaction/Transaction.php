<?php

namespace App\Platform\UseCases\Shared\Transaction;

use Illuminate\Support\Facades\DB;

class Transaction implements TransactionInterface
{
    public function begin(): void
    {
        DB::beginTransaction();
    }

    public function commit(): void
    {
        DB::commit();
    }

    public function rollBack(): void
    {
        DB::rollBack();
    }
}

