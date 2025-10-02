<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\QueryServices\Textbook;

use App\Models\Deal;
use App\Platform\Domains\User\UserId;

readonly class GetPurchasedTextbookDealQueryService
{
    /**
     * 指定したユーザーが購入した特定の教科書の取引詳細を取得
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
                'seller.university',
                'seller.faculty',
                'buyer',
                'dealEvents'
            ])
            ->where('textbook_id', $textbookId)
            ->where('buyer_id', $userId->value)
            ->whereIn('deal_status', ['Purchased', 'Shipping', 'Completed'])
            ->first();
    }
}
