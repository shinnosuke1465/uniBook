<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Faculty;

use App\Exceptions\DomainException;
use App\Platform\Domains\Faculty\Faculty;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\University\UniversityId;

class TestFacultyFactory
{
    /**
     * @throws DomainException
     */
    public static function create(
        FacultyId $id = new FacultyId(),
        String255 $name = new String255('テスト学部'),
        UniversityId $universityId = new UniversityId(),
    ): Faculty {
        return new Faculty(
            $id,
            $name,
            $universityId,
        );
    }
}
