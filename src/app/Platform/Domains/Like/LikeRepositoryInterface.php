<?php

namespace App\Platform\Domains\Like;

interface LikeRepositoryInterface
{
    public function insert(Like $like): void;
}