<?php

declare(strict_types=1);

namespace App\Platform\Domains\Deal;

use App\Exceptions\DomainException;
use App\Platform\Domains\Textbook\TextbookId;

readonly class Deal
{
    public function __construct(
        public DealId $id,
        public Seller $seller,
        public ?Buyer $buyer,
        public TextbookId $textbookId,
        public DealStatus $dealStatus,
    ) {
    }

    public static function create(
        Seller $seller,
        ?Buyer $buyer,
        TextbookId $textbookId,
        DealStatus $dealStatus,
    ): self {
        return new self(
            new DealId(),
            $seller,
            $buyer,
            $textbookId,
            $dealStatus,
        );
    }

    public function purchase(Buyer $buyer): self
    {
        if (!$this->dealStatus->canPurchase()) {
            throw new DomainException('購入できるのは出品中の商品のみです。');
        }

        return new self(
            $this->id,
            $this->seller,
            $buyer,
            $this->textbookId,
            DealStatus::Purchased,
        );
    }

    public function cancel(): self
    {
        if (!$this->dealStatus->canCancel()) {
            throw new DomainException('キャンセルできるのは出品中の商品のみです。');
        }

        return new self(
            $this->id,
            $this->seller,
            $this->buyer,
            $this->textbookId,
            DealStatus::Cancelled,
        );
    }

    public function reportDelivery(): self
    {
        if (!$this->dealStatus->canReportDelivery()) {
            throw new DomainException('配送報告できるのは購入済みの商品のみです。');
        }

        return new self(
            $this->id,
            $this->seller,
            $this->buyer,
            $this->textbookId,
            DealStatus::Shipping,
        );
    }

    public function reportReceipt(): self
    {
        if (!$this->dealStatus->canReportReceipt()) {
            throw new DomainException('受取報告できるのは配送中の商品のみです。');
        }

        return new self(
            $this->id,
            $this->seller,
            $this->buyer,
            $this->textbookId,
            DealStatus::Completed,
        );
    }

    public function update(
        ?Buyer $buyer,
        DealStatus $dealStatus
    ): self{
        return new self(
            $this->id,
            $this->seller,
            $buyer,
            $this->textbookId,
            $dealStatus
        );
    }
}
