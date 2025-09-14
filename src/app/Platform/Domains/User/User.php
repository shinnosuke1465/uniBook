<?php

declare(strict_types=1);

namespace App\Platform\Domains\User;

use App\Exceptions\DomainException;
use App\Platform\Domains\Faculty\Faculty;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Image\Image;
use App\Platform\Domains\Image\ImageId;
use App\Platform\Domains\Shared\Address\Address;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\Name\Name;
use App\Platform\Domains\Shared\PostCode\PostCode;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\University\University;
use App\Platform\Domains\University\UniversityId;

readonly class User
{
    public function __construct(
        public UserId $id,
        public Name $name,
        public String255 $password,
        public PostCode $postCode,
        public Address $address,
        public MailAddress $mailAddress,
        public ?ImageId $imageId,
        public FacultyId $facultyId,
        public UniversityId $universityId,
    ) {
    }

    /**
     * @throws DomainException
     */
    public static function create(
        Name $name,
        String255 $password,
        PostCode $postCode,
        Address $address,
        MailAddress $mailAddress,
        ?Image $image,
        Faculty $faculty,
        University $university,
    ): User {
        // faculty.universityIdとuniversity.idの一致チェック
        if ($faculty->universityId->value !== $university->id->value) {
            throw new DomainException('学部の大学IDとユーザーの大学IDが一致しません。');
        }
        return new self(
            new UserId(),
            $name,
            $password,
            $postCode,
            $address,
            $mailAddress,
            $image?->id,
            $faculty->id,
            $university->id,
        );
    }

}
