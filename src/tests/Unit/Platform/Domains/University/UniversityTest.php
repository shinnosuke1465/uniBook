<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\University;

use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\University\University;
use App\Platform\Domains\University\UniversityId;
use Tests\TestCase;

class UniversityTest extends TestCase
{
    public function test_インスタンスが生成できること(): void
    {
        //given
        $expectedId = new UniversityId();
        $expectedUniversityName = new String255('東京大学');

        //when
        $actualUniversity = new University(
            id: $expectedId,
            name: $expectedUniversityName,
        );

        //then
        $this->assertEquals($expectedId, $actualUniversity->id);
        $this->assertEquals($expectedUniversityName, $actualUniversity->name);
    }

    public function test_staticで生成できること(): void
    {
        //given
        $expectedUniversityName = new String255('東京大学');

        //when
        $actualUniversity = University::create(
            name: $expectedUniversityName,
        );

        //then
        $this->assertInstanceOf(University::class, $actualUniversity);
        $this->assertEquals($expectedUniversityName, $actualUniversity->name);
    }
}
