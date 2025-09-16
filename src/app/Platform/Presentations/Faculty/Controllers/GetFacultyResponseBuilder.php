<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Faculty\Controllers;

use App\Platform\UseCases\Faculty\Dtos\FacultyDto;
readonly class GetFacultyResponseBuilder
{
    public static function toArray(FacultyDto $dtos): array
    {
        return [
            'id' => $dtos->id,
            'name' => $dtos->name,
            'universityId' => $dtos->universityId,
        ];
    }
}
