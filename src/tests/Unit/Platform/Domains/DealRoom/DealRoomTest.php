<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\DealRoom;

use App\Exceptions\DomainException;
use App\Platform\Domains\Deal\DealId;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\DealRoom\DealRoom;
use App\Platform\Domains\DealRoom\DealRoomId;
use App\Platform\Domains\User\UserId;
use App\Platform\Domains\User\UserIdList;
use Tests\TestCase;

class DealRoomTest extends TestCase
{
    /**
     * @throws DomainException
     */
    public function test_インスタンスが生成できること(): void
    {
        // given
        $expectedId = new DealRoomId();
        $expectedDealId = new DealId();
        $expectedUserIds = new UserIdList([new UserId(), new UserId()]);

        // when
        $actualDealRoom = new DealRoom(
            id: $expectedId,
            dealId: $expectedDealId,
            userIds: $expectedUserIds,
        );

        // then
        $this->assertEquals($expectedId, $actualDealRoom->id);
        $this->assertEquals($expectedDealId, $actualDealRoom->dealId);
        $this->assertEquals($expectedUserIds, $actualDealRoom->userIds);
    }

    /**
     * @throws DomainException
     */
    public function test_購入済み状態で取引ルームが作成できること(): void
    {
        // given
        $dealId = new DealId();
        $userIds = new UserIdList([new UserId(), new UserId()]);

        // when
        $actualDealRoom = DealRoom::create(
            dealId: $dealId,
            userIds: $userIds,
        );

        // then
        $this->assertEquals($dealId, $actualDealRoom->dealId);
        $this->assertEquals($userIds, $actualDealRoom->userIds);
        $this->assertInstanceOf(DealRoomId::class, $actualDealRoom->id);
    }

    /**
     * @throws DomainException
     */
    public function test_getUserIdsメソッドでユーザーIDの配列が取得できること(): void
    {
        // given
        $userId1 = new UserId();
        $userId2 = new UserId();
        $userIds = new UserIdList([$userId1, $userId2]);
        $dealRoom = TestDealRoomFactory::create(userIds: $userIds);

        // when
        $actualUserIds = $dealRoom->getUserIds();

        // then
        $this->assertIsArray($actualUserIds);
        $this->assertCount(2, $actualUserIds);
        $this->assertContains($userId1->value, $actualUserIds);
        $this->assertContains($userId2->value, $actualUserIds);
    }

    /**
     * @throws DomainException
     */
    public function test_ユーザーが1人の場合は取引ルームが作成できないこと(): void
    {
        // given
        $dealId = new DealId();
        $userIds = new UserIdList([new UserId()]); // 1人のみ

        // then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('取引ルームには2人のユーザーが必要です。');

        // when
        DealRoom::create(
            dealId: $dealId,
            userIds: $userIds,
        );
    }

    /**
     * @throws DomainException
     */
    public function test_ユーザーが3人の場合は取引ルームが作成できないこと(): void
    {
        // given
        $dealId = new DealId();
        $userIds = new UserIdList([new UserId(), new UserId(), new UserId()]); // 3人

        // then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('取引ルームには2人のユーザーが必要です。');

        // when
        DealRoom::create(
            dealId: $dealId,
            userIds: $userIds,
        );
    }

    public static function invalidDealStatusProvider(): array
    {
        return [
            'Listing' => [DealStatus::Listing],
            'Shipping' => [DealStatus::Shipping],
            'Completed' => [DealStatus::Completed],
            'Cancelled' => [DealStatus::Cancelled],
        ];
    }
}
