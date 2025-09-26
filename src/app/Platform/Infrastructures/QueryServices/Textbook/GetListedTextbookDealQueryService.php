<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\QueryServices\Textbook;

use App\Models\Deal;
use App\Platform\Domains\User\UserId;

readonly class GetListedTextbookDealQueryService
{
    /**
     * 指定したユーザーが出品した特定の教科書の取引詳細を取得
     *
     * @param string $textbookId
     * @param UserId $userId
     * @return Deal|null
     */
    public function getListedProductDeal(string $textbookId, UserId $userId): ?Deal
    {
        return Deal::query()
            ->with([
                'textbook.university',
                'textbook.faculty',
                'textbook.imageIds',
                'seller',
                'buyer',
                'dealEvents'
            ])
            ->where('textbook_id', $textbookId)
            ->where('seller_id', $userId->value)
            ->whereIn('deal_status', ['Listing', 'Completed'])
            ->first();
    }
}