<?php

declare(strict_types=1);

namespace App\Platform\Domains\Deal;

use App\Exceptions\DomainException;

enum DealStatus: string
{
    case Listing = 'Listing';
    case Purchased = 'Purchased';
    case Shipping = 'Shipping';
    case Completed = 'Completed';
    case Cancelled = 'Cancelled';

    /**
     * @throws DomainException
     */
    public static function create(string $status): self
    {
        return match ($status) {
            self::Listing->value => self::Listing,
            self::Purchased->value => self::Purchased,
            self::Shipping->value => self::Shipping,
            self::Completed->value => self::Completed,
            self::Cancelled->value => self::Cancelled,
            default => throw new DomainException('Invalid deal status: ' . $status),
        };
    }

    /**
     * 購入可能かチェック
     */
    public function canPurchase(): bool
    {
        return $this === self::Listing;
    }

    /**
     * キャンセル可能かチェック
     */
    public function canCancel(): bool
    {
        return $this === self::Listing;
    }

    /**
     * 発送報告可能かチェック
     */
    public function canReportDelivery(): bool
    {
        return $this === self::Purchased;
    }

    /**
     * 受取報告可能かチェック
     */
    public function canReportReceipt(): bool
    {
        return $this === self::Shipping;
    }

    /**
     * 購入済みかチェック
     */
    public function isPurchased(): bool
    {
        return in_array($this, [
            self::Purchased,
            self::Shipping,
            self::Completed,
        ], true);
    }

    /**
     * 状態遷移: 購入
     * @throws DomainException
     */
    public function toPurchased(): self
    {
        if (!$this->canPurchase()) {
            throw new DomainException(
                "購入できるのは出品中の商品のみです。現在のステータス: {$this->value}"
            );
        }

        return self::Purchased;
    }

    /**
     * 状態遷移: キャンセル
     * @throws DomainException
     */
    public function toCancelled(): self
    {
        if (!$this->canCancel()) {
            throw new DomainException(
                "キャンセルできるのは出品中の商品のみです。現在のステータス: {$this->value}"
            );
        }

        return self::Cancelled;
    }

    /**
     * 状態遷移: 発送済み
     * @throws DomainException
     */
    public function toShipping(): self
    {
        if (!$this->canReportDelivery()) {
            throw new DomainException(
                "発送報告できるのは購入済みの商品のみです。現在のステータス: {$this->value}"
            );
        }

        return self::Shipping;
    }

    /**
     * 状態遷移: 完了
     * @throws DomainException
     */
    public function toCompleted(): self
    {
        if (!$this->canReportReceipt()) {
            throw new DomainException(
                "受取報告できるのは発送済みの商品のみです。現在のステータス: {$this->value}"
            );
        }

        return self::Completed;
    }
}
