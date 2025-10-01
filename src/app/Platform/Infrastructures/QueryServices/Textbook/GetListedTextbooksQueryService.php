<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\QueryServices\Textbook;

use App\Models\Deal;
use App\Platform\Domains\User\UserId;
use Illuminate\Database\Eloquent\Collection;

readonly class GetListedTextbooksQueryService
{
    /**
     * 指定したユーザーが出品した教科書一覧を取得
     *
     * @param UserId $userId
     * @return Collection<Deal>
     */
    public function getListedTextbooksByUser(UserId $userId): Collection
    {
        return Deal::query()
            ->with([
                'textbook.university',
                'textbook.faculty',
                'textbook.imageIds',
                'seller.university',
                'seller.faculty',
                'buyer',
                'dealEvents'
            ])
            ->where('seller_id', $userId->value)
            ->whereIn('deal_status', ['Listing', 'Completed']) // 出品中・完了済み両方
            ->orderBy('updated_at', 'desc')
            ->get();
    }
}