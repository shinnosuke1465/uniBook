<?php

namespace App\Platform\Domains\DealEvent;

interface DealEventRepositoryInterface
{
    public function insert(DealEvent $dealEvent): void;
}