<?php

namespace App\Platform\Domains\Deal;

interface DealRepositoryInterface
{
    public function insert(Deal $deal): void;
}