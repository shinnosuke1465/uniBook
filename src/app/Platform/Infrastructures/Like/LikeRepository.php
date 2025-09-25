<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\Like;

use App\Exceptions\DuplicateKeyException;
use App\Models\Like as LikeDB;
use App\Platform\Domains\Like\Like;
use App\Platform\Domains\Like\LikeId;
use App\Platform\Domains\Like\LikeRepositoryInterface;

readonly class LikeRepository implements LikeRepositoryInterface
{
    /**
     * @throws DuplicateKeyException
     */
    public function insert(Like $like): void
    {
        if ($this->hasDuplicate($like->id)) {
            throw new DuplicateKeyException('いいねが重複しています。');
        }
        LikeDB::create([
            'id' => $like->id->value,
            'user_id' => $like->userId->value,
            'textbook_id' => $like->textbookId->value,
        ]);
    }

    private function hasDuplicate(LikeId $likeId): bool
    {
        return LikeDB::find($likeId->value) !== null;
    }
}