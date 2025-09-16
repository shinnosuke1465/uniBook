<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\University;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Models\University as UniversityDB;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\University\University;
use App\Platform\Domains\University\UniversityId;
use App\Platform\Domains\University\UniversityRepositoryInterface;

readonly class UniversityRepository implements UniversityRepositoryInterface
{
    public function findAll()
    {
        // TODO: Implement findAll() method.
    }

    /**
     * @throws DomainException
     */
    public function findById(
        UniversityId $universityId
    ): ?University {
        $universityDB = UniversityDB::find($universityId->value);
        if (!$universityDB) {
            return null;
        }
        return new University(
            new UniversityId($universityDB->id),
            new String255($universityDB->name)
        );
    }

    /**
     * @throws DuplicateKeyException
     */
    public function insert(
        University $university
    ): void {
        if ($this->hasDuplicate($university->id)){
            throw new DuplicateKeyException('学部が重複しています。');
        }

        UniversityDB::create(
            [
                'id' => $university->id->value,
                'name' => $university->universityName->value,
            ]
        );
    }

    private function hasDuplicate(UniversityId $universityId): bool
    {
        return UniversityDB::find($universityId->value) !== null;
    }
}
