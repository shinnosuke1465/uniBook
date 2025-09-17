<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Faculty;

use App\Exceptions\DomainException;
use App\Platform\Domains\Faculty\Faculty;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\University\University;
use App\Platform\Domains\University\UniversityId;
use Tests\TestCase;

class FacultyTest extends TestCase
{
    /**
     * @throws DomainException
     */
    public function test_インスタンスが生成できること(): void
    {
        //given
        $expectedId = new FacultyId();
        $expectedFacultyName = new String255('工学部');
        $expectedUniversityId = new UniversityId();

        //when
        $actualFaculty = new Faculty(
            id: $expectedId,
            name: $expectedFacultyName,
            universityId: $expectedUniversityId,
        );

        //then
        $this->assertEquals($expectedId, $actualFaculty->id);
        $this->assertEquals($expectedFacultyName, $actualFaculty->name);
        $this->assertEquals($expectedUniversityId, $actualFaculty->universityId);
    }

    public function test_staticで生成できること(): void
    {
        //given
        $expectedFacultyName = new String255('工学部');
        $expectedUniversityId = new UniversityId();
        $expectedUniversity = new University(
            id: $expectedUniversityId,
            name: new String255('東京大学'),
        );

        //when
        $actualFaculty = Faculty::create(
            name: $expectedFacultyName,
            universityId: $expectedUniversity->id,
        );

        //then
        $this->assertEquals($expectedFacultyName, $actualFaculty->name);
        $this->assertEquals($expectedUniversityId, $actualFaculty->universityId);
    }
}
