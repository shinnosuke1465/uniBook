<?php

namespace App\Platform\UseCases\University;

use App\Platform\Domains\Shared\String\String255;

interface CreateUniversityActionValuesInterface
{
    public function getName(): String255;
}

