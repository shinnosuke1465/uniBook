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
}
