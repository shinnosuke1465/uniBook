<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\QueryServices\User;

use App\Models\Like;
use App\Platform\UseCases\User\Me\Dtos\LikedTextbookDto;
use Illuminate\Database\Eloquent\Collection;

readonly class GetLikedTextbooksDtoFactory
{
    /**
     * LikeモデルのコレクションからLikedTextbookDtoの配列を作成
     *
     * @param Collection<Like> $likes
     * @return LikedTextbookDto[]
     */
    public static function createFromLikes(Collection $likes): array
    {
        return $likes->map(function (Like $like) {
            $textbook = $like->textbook;

            // 画像URLの構築（getImagePath()メソッドを使用）
            $imageUrls = $textbook->imageIds
                ->map(fn($textbookImage) => $textbookImage->image?->getImagePath())
                ->filter() // nullを除外
                ->values()
                ->all();

            // Deal情報の構築（教科書に紐づく取引がある場合）
            $dealInfo = null;
            if ($textbook->deal) {
                $deal = $textbook->deal;
                $dealInfo = [
                    'id' => $deal->id,
                    'is_purchasable' => $deal->deal_status === 'Listing',
                    'seller_info' => [
                        'id' => $deal->seller->id,
                        'nickname' => $deal->seller->name,
                        'profile_image_url' => $deal->seller->image_id,
                    ],
                ];
            }

            // Comments情報の構築
            $comments = $textbook->comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'text' => $comment->text,
                    'created_at' => $comment->created_at?->toISOString(),
                    'user' => [
                        'id' => $comment->user_id,
                        'name' => $comment->user?->name ?? 'Unknown User',
                        'profile_image_url' => $comment->user?->image_id ?? null,
                    ]
                ];
            })->toArray();

            // いいね済みの教科書一覧なので、is_likedは常にtrue
            $isLiked = true;

            return new LikedTextbookDto(
                id: $textbook->id,
                name: $textbook->name,
                price: $textbook->price,
                description: $textbook->description ?? '',
                imageUrls: $imageUrls,
                universityName: $textbook->university->name ?? '',
                facultyName: $textbook->faculty->name ?? '',
                conditionType: $textbook->condition_type,
                deal: $dealInfo,
                comments: $comments,
                isLiked: $isLiked,
            );
        })->all();
    }
}