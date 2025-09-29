<?php

declare(strict_types=1);

namespace App\Platform\Presentations\DealRoom\Controllers;

use App\Platform\UseCases\DealRoom\Dtos\DealRoomDto;

readonly class GetDealRoomResponseBuilder
{
    public static function toArray(DealRoomDto $dto): array
    {
        return [
            'id' => $dto->id,
            'deal_id' => $dto->dealId,
            'user_ids' => $dto->userIds,
            'created_at' => $dto->createdAt,
            'updated_at' => $dto->updatedAt,
        ];
    }
}