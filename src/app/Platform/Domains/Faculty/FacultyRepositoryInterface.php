<?php

namespace App\Platform\Domains\Faculty;

use App\Platform\Domains\University\UniversityId;

interface FacultyRepositoryInterface
{
    public function findAll();
    public function findById(FacultyId $facultyId): ?Faculty;

    public function findByUniversityId(UniversityId $universityId);

    public function insert(Faculty $faculty): void;
}
