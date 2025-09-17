<?php

namespace App\Platform\UseCases\Faculty;

use App\Platform\Domains\University\UniversityId;

interface GetFacultiesActionValuesInterface
{
    public function getUniversityId(): UniversityId;
}
