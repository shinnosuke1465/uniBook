<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\User;

use App\Platform\Domains\Faculty\Faculty;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Shared\Address\Address;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\Name\Name;
use App\Platform\Domains\Shared\PostCode\PostCode;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\University\University;
use App\Platform\Domains\University\UniversityId;
use App\Platform\Domains\User\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_staticで作成できること(): void
    {
        //given
        $expectName = new Name('testName');
        $expectPassword = new String255('testPassword');
        $expectPostCode = new PostCode(new String255('1234567'));
        $expectAddress = new Address(new String255('testAddress'));
        $expectMailAddress = new MailAddress(new String255('a@a.com'));
        $universityId = new UniversityId();
        $expectFaculty = new Faculty(
            new FacultyId(),
            new String255('工学部'),
            $universityId,
        );
        $expectUniversity = new University(
            $universityId,
            new String255('東京大学'),
        );

        //when
        $actualUser = User::create(
            name: $expectName,
            password: $expectPassword,
            postCode: $expectPostCode,
            address: $expectAddress,
            mailAddress: $expectMailAddress,
            image: $image = null,
            faculty: $expectFaculty,
            university: $expectUniversity,
        );

        //then
        $this->assertEquals($expectName, $actualUser->name);
        $this->assertEquals($expectPassword, $actualUser->password);
        $this->assertEquals($expectPostCode, $actualUser->postCode);
        $this->assertEquals($expectAddress, $actualUser->address);
        $this->assertEquals($expectMailAddress, $actualUser->mailAddress);
        $this->assertEquals($expectFaculty->id, $actualUser->facultyId);
        $this->assertEquals($expectUniversity->id, $actualUser->universityId);
    }
}
