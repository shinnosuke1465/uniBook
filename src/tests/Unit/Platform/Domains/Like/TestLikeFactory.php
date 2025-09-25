<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Like;

use App\Platform\Domains\Like\Like;
use App\Platform\Domains\Like\LikeId;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\User\UserId;

class TestLikeFactory
{
    public static function create(
        LikeId $id = new LikeId(),
        UserId $userId = new UserId(),
        TextbookId $textbookId = new TextbookId(),
    ): Like {
        return new Like(
            $id,
            $userId,
            $textbookId,
        );
    }
}