<?php

namespace App\Platform\UseCases\Faculty;

use App\Platform\Domains\Faculty\FacultyId;

interface GetFacultyActionValuesInterface
{
    public function getFacultyId(): FacultyId;
}
