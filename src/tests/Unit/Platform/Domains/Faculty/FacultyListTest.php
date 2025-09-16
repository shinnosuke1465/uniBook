<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Faculty;

use App\Platform\Domains\Faculty\FacultyList;
use Tests\TestCase;
use App\Exceptions\DomainException;

class FacultyListTest extends TestCase
{
    /**
     * @throws DomainException
     */
    public function test_要素数を取得できること(): void
    {
        //given
        $facultyList = new FacultyList([
            TestFacultyFactory::create(),
            TestFacultyFactory::create(),
        ]);
        $expectedCount = 2;

        //when
        $actualCount = $facultyList->count();

        //then
        $this->assertSame($expectedCount, $actualCount);
    }

    /**
     * @throws DomainException
     */
    public function test_重複したFacultyIdがある場合は例外が発生すること(): void
    {
        $this->expectException(DomainException::class);
        $faculty = TestFacultyFactory::create();
        new FacultyList([$faculty, $faculty]);
    }

    /**
     * @throws DomainException
     */
    public function test_toArrayで配列に変換できること(): void
    {
        //given
        $faculty1 = TestFacultyFactory::create();
        $faculty2 = TestFacultyFactory::create();
        $facultyList = new FacultyList([$faculty1, $faculty2]);

        //when
        $actualArray = $facultyList->toArray();

        //then
        $this->assertCount(2, $actualArray);
        $this->assertEquals($faculty1, $actualArray[0]);
        $this->assertEquals($faculty2, $actualArray[1]);
    }

    /**
     * @throws DomainException
     */
    public function test_isEmptyで空判定できること(): void
    {
        //given
        $facultyList = new FacultyList([]);

        //when
        $actual = $facultyList->isEmpty();

        //then
        $this->assertTrue($actual);
    }
}
