<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Like;

use App\Platform\Domains\Like\Like;
use App\Platform\Domains\Like\LikeId;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\User\UserId;
use Tests\TestCase;

class LikeTest extends TestCase
{
    public function test_インスタンスが生成できること(): void
    {
        //given
        $expectId = new LikeId();
        $expectUserId = new UserId();
        $expectTextbookId = new TextbookId();

        //when
        $actualLike = new Like(
            id: $expectId,
            userId: $expectUserId,
            textbookId: $expectTextbookId,
        );

        //then
        $this->assertEquals($expectId, $actualLike->id);
        $this->assertEquals($expectUserId, $actualLike->userId);
        $this->assertEquals($expectTextbookId, $actualLike->textbookId);
    }

    public function test_staticで作成できること(): void
    {
        //given
        $expectUserId = new UserId();
        $expectTextbookId = new TextbookId();

        //when
        $actualLike = Like::create(
            userId: $expectUserId,
            textbookId: $expectTextbookId,
        );

        //then
        $this->assertEquals($expectUserId, $actualLike->userId);
        $this->assertEquals($expectTextbookId, $actualLike->textbookId);
        $this->assertInstanceOf(LikeId::class, $actualLike->id);
    }
}