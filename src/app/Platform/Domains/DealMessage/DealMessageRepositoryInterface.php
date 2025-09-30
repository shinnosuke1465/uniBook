<?php

declare(strict_types=1);

namespace App\Platform\Domains\DealMessage;

interface DealMessageRepositoryInterface
{
    public function save(DealMessage $dealMessage): void;

    public function findById(DealMessageId $id): ?DealMessage;
}