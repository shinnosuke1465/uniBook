<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\Faculty;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Platform\Domains\Faculty\Faculty;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Faculty\FacultyRepositoryInterface;
use App\Models\Faculty as FacultyDB;
use App\Platform\Domains\University\UniversityId;

readonly class FacultyRepository implements FacultyRepositoryInterface
{
    public function findAll()
    {
        // TODO: Implement findAll() method.
    }

    /**
     * @throws DomainException
     */
    public function findById(
        FacultyId $facultyId
    ): ?Faculty {
        $facultyModel = FacultyDB::find($facultyId->value);

        if ($facultyModel === null) {
            return null;
        }

        return FacultyFactory::create($facultyModel);
    }

    /**
     * @throws DomainException
     */
    public function findByUniversityId(UniversityId $universityId): array
    {
        $facultyModels = FacultyDB::where('university_id', $universityId->value)->get();

        $faculties = [];
        foreach ($facultyModels as $facultyModel) {
            $faculties[] = FacultyFactory::create($facultyModel);
        }

        return $faculties;
    }

    /**
     * @throws DuplicateKeyException
     */
    public function insert(
        Faculty $faculty
    ): void {
        if ($this->hasDuplicate($faculty->id)){
            throw new DuplicateKeyException('学部が重複しています。');
        }

        FacultyDB::create(
            [
                'id' => $faculty->id->value,
                'name' => $faculty->name->value,
                'university_id' => $faculty->universityId->value,
            ]
        );
    }

    private function hasDuplicate(FacultyId $facultyId): bool
    {
        return FacultyDB::find($facultyId->value) !== null;
    }
}
