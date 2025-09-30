<?php

namespace App\Platform\Domains\Deal;

use App\Platform\Domains\Textbook\TextbookId;

interface DealRepositoryInterface
{
    public function insert(Deal $deal): void;

    public function findByTextbookId(TextbookId $textbookId): ?Deal;

    public function update(Deal $deal): void;
}
