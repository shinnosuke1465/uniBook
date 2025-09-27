<?php

declare(strict_types=1);

namespace App\Policies;

use App\Platform\Domains\Deal\Deal;
use App\Platform\Domains\User\User;

readonly class DealPolicy
{
    /**
     * 商品購入API
     *
     * @param User $user
     * @param Deal $deal
     * @return bool
     */
    public function purchaseDeal(User $user, Deal $deal): bool
    {
        // 出品者が自分ではない場合のみアクセス可
        return $user->id != $deal->seller->userId->value;
    }

    /**
     * 商品出品キャンセルAPI
     *
     * @param User $user
     * @param Deal $deal
     * @return bool
     */
    public function cancelDeal(User $user, Deal $deal): bool
    {
        // 出品者が自分である場合のみアクセス可
        return $user->id == $deal->seller->userId->value;
    }

    /**
     * 配送報告API
     *
     * @param User $user
     * @param Deal $deal
     * @return bool
     */
    public function reportDeliveryDeal(User $user, Deal $deal): bool
    {
        // 出品者が自分である場合のみアクセス可
        return $user->id == $deal->seller->userId->value;
    }

    /**
     * 受取報告API
     *
     * @param User $user
     * @param Deal $deal
     * @return bool
     */
    public function reportReceiptDeal(User $user, Deal $deal): bool
    {
        // 購入者が自分である場合のみアクセス可
        return $user->id == $deal->buyer->userId->value;
    }
}
