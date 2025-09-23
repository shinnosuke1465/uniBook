<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Comment;

use App\Exceptions\DomainException;
use App\Platform\Domains\Comment\Comment;
use App\Platform\Domains\Comment\CommentId;
use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\User\UserId;
use Tests\TestCase;

class CommentTest extends TestCase
{
    /**
     * @throws DomainException
     */
    public function test_インスタンスが生成できること(): void
    {
        //given
        $expectId = new CommentId();
        $expectText = new Text('テストコメント内容');
        $expectUserId = new UserId();
        $expectTextbookId = new TextbookId();

        //when
        $actualComment = new Comment(
            id: $expectId,
            text: $expectText,
            userId: $expectUserId,
            textbookId: $expectTextbookId,
        );

        //then
        $this->assertEquals($expectId, $actualComment->id);
        $this->assertEquals($expectText, $actualComment->text);
        $this->assertEquals($expectUserId, $actualComment->userId);
        $this->assertEquals($expectTextbookId, $actualComment->textbookId);
    }
    /**
     * @throws DomainException
     */
    public function test_staticで作成できること(): void
    {
        //given
        $expectText = new Text('テストコメント内容');
        $expectUserId = new UserId();
        $expectTextbookId = new TextbookId();

        //when
        $actualComment = Comment::create(
            text: $expectText,
            userId: $expectUserId,
            textbookId: $expectTextbookId,
        );

        //then
        $this->assertEquals($expectText, $actualComment->text);
        $this->assertEquals($expectUserId, $actualComment->userId);
        $this->assertEquals($expectTextbookId, $actualComment->textbookId);
    }
}
