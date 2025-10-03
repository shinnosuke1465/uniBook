<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Textbook\Dtos;

use App\Models\Textbook as TextbookDB;
use Illuminate\Support\Facades\Auth;

readonly class TextbookWithRelationsDto
{
    public function __construct(
        public string $id,
        public string $name,
        public int $price,
        public string $description,
        public array $imageUrls,
        public string $universityId,
        public string $universityName,
        public string $facultyId,
        public string $facultyName,
        public string $conditionType,
        public ?array $deal,
        public array $comments,
        public bool $isLiked,
    ) {
    }

    public static function fromEloquentModel(TextbookDB $textbookModel, ?string $currentUserId = null): self
    {
        // Deal情報の構築
        $deal = null;
        if ($textbookModel->deal) {
            $deal = [
                'id' => $textbookModel->deal->id,
                'is_purchasable' => $textbookModel->deal->deal_status === 'Listing',
                'seller_info' => [
                    'id' => $textbookModel->deal->seller->id,
                    'nickname' => $textbookModel->deal->seller->name,
                    'profile_image_url' => $textbookModel->deal->seller->image_id,
                ]
            ];
        }

        // Comments情報の構築
        $comments = $textbookModel->comments->map(function ($comment) {
            return [
                'id' => $comment->id,
                'text' => $comment->text,
                'created_at' => $comment->created_at?->toISOString(),
                'user' => [
                    'id' => $comment->user_id,
                    'name' => $comment->user?->name ?? 'Unknown User',
                    'profile_image_url' => null, // TODO: 実装
                ]
            ];
        })->all();

        // is_liked の判定
        $isLiked = false;
        if ($currentUserId) {
            $isLiked = $textbookModel->likes->contains('user_id', $currentUserId);
        }

        // imageUrls の生成（getImagePath()メソッドを使用）
        $imageUrls = $textbookModel->imageIds
            ->map(fn($textbookImage) => $textbookImage->image?->getImagePath())
            ->filter() // nullを除外
            ->values()
            ->all();

        return new self(
            $textbookModel->id,
            $textbookModel->name,
            $textbookModel->price,
            $textbookModel->description ?? '',
            $imageUrls,
            $textbookModel->university_id ?? '',
            $textbookModel->university->name ?? '',
            $textbookModel->faculty_id ?? '',
            $textbookModel->faculty->name ?? '',
            $textbookModel->condition_type,
            $deal,
            $comments,
            $isLiked,
        );
    }
}
