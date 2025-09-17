<?php

namespace App\Platform\UseCases\University;

use App\Platform\Domains\University\UniversityId;

interface GetUniversityActionValuesInterface
{
    public function getUniversityId(): UniversityId;
}

