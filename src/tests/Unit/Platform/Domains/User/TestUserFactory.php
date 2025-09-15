<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\User;

use App\Exceptions\DomainException;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Image\ImageId;
use App\Platform\Domains\University\UniversityId;
use App\Platform\Domains\User\User;
use App\Platform\Domains\User\UserId;
use App\Platform\Domains\Shared\Name\Name;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\Shared\PostCode\PostCode;
use App\Platform\Domains\Shared\Address\Address;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Image\Image;
use App\Platform\Domains\Faculty\Faculty;
use App\Platform\Domains\University\University;

class TestUserFactory
{
    /**
     * @throws DomainException
     */
    public static function create(
        UserId $id = new UserId(),
        Name $name = new Name('テスト太郎'),
        String255 $password = new String255('password123'),
        PostCode $postCode = new PostCode(new String255('1234567')),
        Address $address = new Address(new String255('テスト市1-2-3')),
        MailAddress $mailAddress = new MailAddress(new String255('test@test.com')),
        ?ImageId $imageId = null,
        FacultyId $facultyId = new FacultyId(),
        UniversityId $universityId = new UniversityId(),
    ): User {
        return new User(
            $id,
            $name,
            $password,
            $postCode,
            $address,
            $mailAddress,
            $imageId,
            $facultyId,
            $universityId,
        );
    }
}
