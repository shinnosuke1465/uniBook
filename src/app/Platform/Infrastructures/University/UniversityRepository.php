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
    public function findAll(): array
    {
        $universityModels = UniversityDB::query()->get();

        return $universityModels->map(
            fn ($universityModel) => UniversityFactory::create($universityModel)
        )->all();
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
        return UniversityFactory::create($universityDB);
    }

    /**
     * @throws DomainException
     */
    public function findByName(String255 $name): ?University
    {
        $universityDB = UniversityDB::where('name', $name->value)->first();
        if (!$universityDB) {
            return null;
        }

        return UniversityFactory::create($universityDB);
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
                'name' => $university->name->value,
            ]
        );
    }

    private function hasDuplicate(UniversityId $universityId): bool
    {
        return UniversityDB::find($universityId->value) !== null;
    }
}
