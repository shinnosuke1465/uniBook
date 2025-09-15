<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\Faculty;

use App\Exceptions\DomainException;
use App\Platform\Domains\Faculty\Faculty;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\University\UniversityId;
use App\Models\Faculty as FacultyDB;

readonly class FacultyFactory
{
    /**
     * @throws DomainException
     */
    public static function create(
        FacultyDB $facultyDB,
    ): Faculty {
        return new Faculty(
            new FacultyId($facultyDB->id),
            new String255($facultyDB->name),
            new UniversityId($facultyDB->university_id),
        );
    }
}
