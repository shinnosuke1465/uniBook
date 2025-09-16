<?php

namespace App\Platform\UseCases\Faculty;

use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\University\UniversityId;

interface CreateFacultyActionValuesInterface
{
    public function getName(): String255;

    public function getUniversityId(): UniversityId;
}
