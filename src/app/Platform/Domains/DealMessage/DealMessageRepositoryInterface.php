<?php

declare(strict_types=1);

namespace App\Platform\Domains\DealMessage;

interface DealMessageRepositoryInterface
{
    public function insert(DealMessage $dealMessage): void;
}
