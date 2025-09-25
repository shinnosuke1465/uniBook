<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\QueryServices\Product;

use App\Models\Deal;
use App\Platform\Domains\User\UserId;
use Illuminate\Database\Eloquent\Collection;

readonly class GetPurchasedProductsQueryService
{
    /**
     * 指定したユーザーが購入した商品一覧を取得
     *
     * @param UserId $userId
     * @return Collection<Deal>
     */
    public function getPurchasedProductsByUser(UserId $userId): Collection
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
            ->where('buyer_id', $userId->value)
            ->where('deal_status', 'Completed')
            ->orderBy('updated_at', 'desc')
            ->get();
    }
}