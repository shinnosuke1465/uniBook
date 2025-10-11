<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\DealMessage;

use App\Exceptions\DomainException;
use App\Platform\Domains\Deal\DealId;
use App\Platform\Domains\DealMessage\DealMessageDomainService;
use App\Platform\Domains\DealRoom\DealRoom;
use App\Platform\Domains\DealRoom\DealRoomId;
use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Domains\User\UserId;
use App\Platform\Domains\User\UserIdList;
use Tests\TestCase;

class DealMessageDomainServiceTest extends TestCase
{
    private DealMessageDomainService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DealMessageDomainService();
    }

    public function test_DealRoomに参加しているユーザーはメッセージを作成できること(): void
    {
        // given
        $sellerId = new UserId('11111111-1111-1111-1111-111111111111');
        $buyerId = new UserId('22222222-2222-2222-2222-222222222222');

        $dealRoom = new DealRoom(
            new DealRoomId(),
            new DealId(),
            new UserIdList([$sellerId, $buyerId])
        );

        $message = new Text('こんにちは');

        // when
        $dealMessage = $this->service->createMessage($dealRoom, $sellerId, $message);

        // then
        $this->assertEquals($dealRoom->id, $dealMessage->dealRoomId);
        $this->assertEquals($sellerId, $dealMessage->sender->userId);
        $this->assertEquals($message, $dealMessage->message);
    }

    public function test_DealRoomに参加していないユーザーはメッセージを作成できないこと(): void
    {
        // given
        $sellerId = new UserId('11111111-1111-1111-1111-111111111111');
        $buyerId = new UserId('22222222-2222-2222-2222-222222222222');
        $outsiderId = new UserId('33333333-3333-3333-3333-333333333333'); // 参加していない

        $dealRoom = new DealRoom(
            new DealRoomId(),
            new DealId(),
            new UserIdList([$sellerId, $buyerId])
        );

        $message = new Text('こんにちは');

        // then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('この取引ルームにメッセージを送信する権限がありません。');

        // when
        $this->service->createMessage($dealRoom, $outsiderId, $message);
    }

    public function test_購入者もメッセージを作成できること(): void
    {
        // given
        $sellerId = new UserId('11111111-1111-1111-1111-111111111111');
        $buyerId = new UserId('22222222-2222-2222-2222-222222222222');

        $dealRoom = new DealRoom(
            new DealRoomId(),
            new DealId(),
            new UserIdList([$sellerId, $buyerId])
        );

        $message = new Text('商品届きました！');

        // when
        $dealMessage = $this->service->createMessage($dealRoom, $buyerId, $message);

        // then
        $this->assertEquals($dealRoom->id, $dealMessage->dealRoomId);
        $this->assertEquals($buyerId, $dealMessage->sender->userId);
        $this->assertEquals($message, $dealMessage->message);
    }
}