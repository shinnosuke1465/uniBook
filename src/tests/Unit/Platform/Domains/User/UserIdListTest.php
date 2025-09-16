<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\User;

use App\Exceptions\DomainException;
use App\Platform\Domains\User\UserId;
use App\Platform\Domains\User\UserIdList;
use Tests\TestCase;

class UserIdListTest extends TestCase
{
    /**
     * @throws DomainException
     */
    public function test_インスタンス化した時に昇順ソートされること(): void
    {
        //given
        $userId1 = new UserId('00000000-0000-0000-0000-000000000001');
        $userId2 = new UserId('00000000-0000-0000-0000-000000000002');
        $userId3 = new UserId('00000000-0000-0000-0000-000000000003');
        $unsortedUserIds = [
            $userId3,
            $userId1,
            $userId2,
        ];
        $expectedUserIds = [
            $userId1,
            $userId2,
            $userId3,
        ];

        //when
        $userIdList = new UserIdList($unsortedUserIds);

        //then
        $this->assertEquals($expectedUserIds, $userIdList->toArray());
    }

    public function test_重複したIdがある場合に例外が発生すること(): void
    {
        //given
        $userId1 = new UserId('00000000-0000-0000-0000-000000000001');
        $userId2 = new UserId('00000000-0000-0000-0000-000000000002');
        $duplicateUserIds = [
            $userId1,
            $userId2,
            $userId1,
        ];

        //then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('userIdが同じオブジェクトが存在しています。');

        //when
        new UserIdList($duplicateUserIds);
    }
}
