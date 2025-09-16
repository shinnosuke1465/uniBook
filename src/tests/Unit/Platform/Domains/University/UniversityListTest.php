<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\University;

use App\Platform\Domains\University\UniversityList;
use Tests\TestCase;
use App\Exceptions\DomainException;

class UniversityListTest extends TestCase
{
    /**
     * @throws DomainException
     */
    public function test_要素数を取得できること(): void
    {
        //given
        $universityList = new UniversityList([
            TestUniversityFactory::create(),
            TestUniversityFactory::create(),
        ]);
        $expectedCount = 2;

        //when
        $actualCount = $universityList->count();

        //then
        $this->assertSame($expectedCount, $actualCount);
    }

    /**
     * @throws DomainException
     */
    public function test_重複したUniversityIdがある場合は例外が発生すること(): void
    {
        $this->expectException(DomainException::class);
        $university = TestUniversityFactory::create();
        new UniversityList([$university, $university]);
    }

    /**
     * @throws DomainException
     */
    public function test_toArrayで配列に変換できること(): void
    {
        //given
        $university1 = TestUniversityFactory::create();
        $university2 = TestUniversityFactory::create();
        $universityList = new UniversityList([$university1, $university2]);

        //when
        $actualArray = $universityList->toArray();

        //then
        $this->assertCount(2, $actualArray);
        $this->assertEquals($university1, $actualArray[0]);
        $this->assertEquals($university2, $actualArray[1]);
    }

    /**
     * @throws DomainException
     */
    public function test_isEmptyで空判定できること(): void
    {
        //given
        $universityList = new UniversityList([]);

        //when
        $actual = $universityList->isEmpty();

        //then
        $this->assertTrue($actual);
    }
}

