<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\User;

use App\Exceptions\DomainException;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\User\User;
use App\Platform\Domains\User\UserList;
use Tests\TestCase;

class UserListTest extends TestCase
{
    /**
     * @throws DomainException
     */
    public function test_インスタンスが作成できること(): void
    {
        //given
        $users = $this->createUsers();

        //when
        $userList = new UserList($users);

        //then
        $this->assertEquals($users, $userList->toArray());
    }

    /**
     * @throws DomainException
     */
    public function test_空のリストを作成できること(): void
    {
        //when
        $userList = new UserList([]);

        //then
        $this->assertEmpty($userList->toArray());
    }

    public function test_重複したIDがある場合に例外が発生すること(): void
    {
        //given
        $users = $this->createUsers();
        $users[] = $users[0]; // 重複したユーザーを追加

        //when
        //then
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('userIdが同じオブジェクトが存在しています。');
        new UserList($users);
    }

    public function test_配列に変換できること(): void
    {
        //given
        $users = $this->createUsers();
        $userList = new UserList($users);

        //when
        $array = $userList->toArray();

        //then
        $this->assertEquals($users, $array);
    }

    /**
     * @return User[]
     * @throws DomainException
     */
    private function createUsers(): array
    {
        return [
            TestUserFactory::create(),
            TestUserFactory::create(
                mailAddress: new MailAddress(new String255('a@aaaaa.com'))
            ),
        ];
    }
}
