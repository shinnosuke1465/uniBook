<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\Faculty;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Platform\Domains\Faculty\Faculty;
use App\Platform\Domains\Faculty\FacultyId;
use App\Platform\Domains\Faculty\FacultyRepositoryInterface;
use App\Models\Faculty as FacultyDB;
readonly class FacultyRepository implements FacultyRepositoryInterface
{
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
                'name' => $faculty->facultyName->value,
                'university_id' => $faculty->universityId->value,
            ]
        );
    }

    private function hasDuplicate(FacultyId $facultyId): bool
    {
        return FacultyDB::find($facultyId->value) !== null;
    }
}
