<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\Like;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Exceptions\RepositoryException;
use App\Models\Like as LikeDB;
use App\Platform\Domains\Like\Like;
use App\Platform\Domains\Like\LikeId;
use App\Platform\Domains\Like\LikeRepositoryInterface;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\User\UserId;

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

    public function delete(UserId $userId, TextbookId $textbookId): void
    {
        LikeDB::where('user_id', $userId->value)
            ->where('textbook_id', $textbookId->value)
            ->delete();
    }

    /**
     * ユーザーIDと教科書IDでいいねを取得
     * @throws DomainException
     */
    public function findByUserIdAndTextbookId(UserId $userId, TextbookId $textbookId): ?Like
    {
        $likeDB = LikeDB::where('user_id', $userId->value)
            ->where('textbook_id', $textbookId->value)
            ->first();

        if ($likeDB === null) {
            return null;
        }

        return new Like(
            new LikeId($likeDB->id),
            new UserId($likeDB->user_id),
            new TextbookId($likeDB->textbook_id)
        );
    }

    private function hasDuplicate(LikeId $likeId): bool
    {
        return LikeDB::find($likeId->value) !== null;
    }
}
