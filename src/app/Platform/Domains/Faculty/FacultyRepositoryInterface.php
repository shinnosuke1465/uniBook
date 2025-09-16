<?php

namespace App\Platform\Domains\Faculty;

interface FacultyRepositoryInterface
{
    public function findAll();
    public function findById(FacultyId $facultyId): ?Faculty;

    public function insert(Faculty $faculty): void;
}
