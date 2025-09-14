<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Shared\Name;

use App\Platform\Domains\Shared\Name\Name;
use Tests\TestCase;

class NameTest extends TestCase
{
    public function test_オブジェクトが生成できること(): void
    {
       //given
        $expectName = '山田太郎';

        //when
        $actualName = new Name($expectName);

        //then
        $this->assertEquals($expectName, $actualName->name);
    }

    public function test_getNameで値が取得できること(): void
    {
        // given
        $expectName = '山田太郎';
        $name = new Name($expectName);

        // when
        $actual = $name->getName();

        // then
        $this->assertEquals($expectName, $actual);
    }
}
