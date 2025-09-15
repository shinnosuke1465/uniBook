<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\University;

use App\Exceptions\DomainException;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\University\University;
use App\Models\University as UniversityDB;
use App\Platform\Domains\University\UniversityId;

readonly class UniversityFactory
{
    /**
     * @throws DomainException
     */
    public static function create(
        UniversityDB $universityDB,
    ): University {
        return new University(
            new UniversityId($universityDB->id),
            new String255($universityDB->name),
        );
    }
}
