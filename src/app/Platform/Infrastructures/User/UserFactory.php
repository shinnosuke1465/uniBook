<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\User;

use App\Exceptions\DomainException;
use App\Models\User as UserDB;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Image\ImageId;
use App\Platform\Domains\Shared\Address\Address;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\Name\Name;
use App\Platform\Domains\Shared\PostCode\PostCode;
use App\Platform\Domains\University\UniversityId;
use App\Platform\Domains\User\User;
use App\Platform\Domains\User\UserId;
use App\Platform\Domains\Shared\String\String255;


class UserFactory
{
    /**
     * @throws DomainException
     */
    public static function create(
        UserDB $userDB
    ): User {
        $imageId = $userDB->image_id ? new ImageId($userDB->image_id) : null;
        return new User(
            new UserId($userDB->id),
            new Name($userDB->name),
            new String255($userDB->password),
            new PostCode(new String255($userDB->post_code)),
            new Address(new String255($userDB->address)),
            new MailAddress(new String255($userDB->mail_address)),
            $imageId,
            new FacultyId($userDB->faculty_id),
            new UniversityId($userDB->university_id),
        );
    }
}
