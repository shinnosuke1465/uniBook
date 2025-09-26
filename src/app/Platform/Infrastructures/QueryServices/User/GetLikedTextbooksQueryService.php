<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\QueryServices\User;

use App\Models\Like;
use App\Platform\Domains\User\UserId;
use Illuminate\Database\Eloquent\Collection;

readonly class GetLikedTextbooksQueryService
{
    /**
     * 指定したユーザーがいいねした教科書一覧を取得
     *
     * @param UserId $userId
     * @return Collection<Like>
     */
    public function getLikedTextbooksByUser(UserId $userId): Collection
    {
        return Like::query()
            ->with([
                'textbook.university',
                'textbook.faculty',
                'textbook.imageIds',
                'textbook.deal.seller',
                'textbook.deal.buyer',
                'textbook.comments.user',
            ])
            ->where('user_id', $userId->value)
            ->orderBy('created_at', 'desc') // いいねした日時順
            ->get();
    }
}