<?php

declare(strict_types=1);

namespace App\Platform\Domains\Deal;

use App\Exceptions\DomainException;
use App\Platform\Domains\DealEvent\DealEvent;
use App\Platform\Domains\Textbook\TextbookId;

/**
 * DealとDealEventの整合性を保証するドメインサービス
 *
 * 責務:
 * - Dealの状態遷移とDealEventの生成を同時に行う
 * - 2つの集約間の整合性を保証する
 * - 永続化は行わない（UseCaseの責務）
 */
readonly class DealDomainService
{
    /**
     * 購入処理
     *
     * @return array{Deal, DealEvent}
     * @throws DomainException
     */
    public function purchase(Deal $deal, Buyer $buyer): array
    {
        // Dealの状態遷移
        $updatedDeal = $deal->purchase($buyer);

        // DealEventの生成
        $dealEvent = DealEvent::purchased($buyer, $deal->id);

        return [$updatedDeal, $dealEvent];
    }

    /**
     * キャンセル処理
     *
     * @return array{Deal, DealEvent}
     * @throws DomainException
     */
    public function cancel(Deal $deal): array
    {
        // Dealの状態遷移（Deal内でバリデーションが行われる）
        $updatedDeal = $deal->cancel();

        // DealEventの生成
        $dealEvent = DealEvent::cancelled($deal->seller, $deal->id);

        return [$updatedDeal, $dealEvent];
    }

    /**
     * 配送報告処理
     *
     * @return array{Deal, DealEvent}
     * @throws DomainException
     */
    public function reportDelivery(Deal $deal): array
    {
        // Dealの状態遷移（Deal内でバリデーションが行われる）
        $updatedDeal = $deal->reportDelivery();

        // DealEventの生成
        $dealEvent = DealEvent::delivered($deal->seller, $deal->id);

        return [$updatedDeal, $dealEvent];
    }

    /**
     * 受取報告処理
     *
     * @return array{Deal, DealEvent}
     * @throws DomainException
     */
    public function reportReceipt(Deal $deal): array
    {
        // Dealの状態遷移（Deal内でバリデーションが行われる）
        $updatedDeal = $deal->reportReceipt();

        // DealEventの生成
        $dealEvent = DealEvent::received($deal->buyer, $deal->id);

        return [$updatedDeal, $dealEvent];
    }

    /**
     * 出品処理（Deal作成 + DealEvent生成）
     *
     * @return array{Deal, DealEvent}
     * @throws DomainException
     */
    public function createListing(Seller $seller, TextbookId $textbookId): array
    {
        // Dealを作成
        $deal = Deal::create(
            $seller,
            null,
            $textbookId,
            DealStatus::create('Listing')
        );

        // DealEventの生成
        $dealEvent = DealEvent::listed($seller, $deal->id);

        return [$deal, $dealEvent];
    }
}
