<?php

namespace App\Platform\UseCases\User;

use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Image\ImageId;
use App\Platform\Domains\Shared\Address\Address;
use App\Platform\Domains\Shared\MailAddress\MailAddress;
use App\Platform\Domains\Shared\Name\Name;
use App\Platform\Domains\Shared\PostCode\PostCode;
use App\Platform\Domains\Shared\String\NonEmptyString255;
use App\Platform\Domains\University\UniversityId;

interface CreateUserValuesInterface
{
    public function getName(): Name;

    public function getUserPassword(): NonEmptyString255;

    public function getPostCode(): PostCode;

    public function getAddress(): Address;

    public function getMailAddress(): MailAddress;

    public function getImageId(): ?ImageId;

    public function getFacultyId(): FacultyId;

    public function getUniversityId(): UniversityId;
}
