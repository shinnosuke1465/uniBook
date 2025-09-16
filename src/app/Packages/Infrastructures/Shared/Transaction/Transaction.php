<?php

declare(strict_types=1);

namespace App\Packages\Infrastructures\Shared\Transaction;

use App\Platform\UseCases\Shared\Transaction\TransactionInterface;
use DB;
use Throwable;

class Transaction implements TransactionInterface
{
    /**
     * @throws Throwable
     */
    public function begin(): void
    {
        DB::beginTransaction();
    }

    /**
     * 予約時は排他制御を行う
     *
     * @throws Throwable
     */
    public function beginWithReadCommitted(): void
    {
        $pdo = DB::connection()->getPdo();
        $pdo->exec('SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;');

        DB::beginTransaction();
    }

    /**
     * @throws Throwable
     */
    public function rollback(): void
    {
        DB::rollBack();

        // ISOLATION LEVELをリセット
        $pdo = DB::connection()->getPdo();
        $pdo->exec('SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;');
    }

    /**
     * @throws Throwable
     */
    public function commit(): void
    {
        DB::commit();

        // ISOLATION LEVELをリセット
        $pdo = DB::connection()->getPdo();
        $pdo->exec('SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ;');
    }
}
