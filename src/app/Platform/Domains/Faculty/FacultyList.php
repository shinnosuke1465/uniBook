<?php

declare(strict_types=1);

namespace App\Platform\Domains\Faculty;

use Illuminate\Support\Collection;
use App\Exceptions\DomainException;

readonly class FacultyList
{
    /**
     * @var Collection <int, Faculty>
     */
    private Collection $collection;

    /**
     * @param Faculty[] $faculties
     * @throws DomainException
     */
    public function __construct(array $faculties)
    {
        $this->collection = collect($faculties)->mapWithKeys(
            fn (Faculty $faculty) => [$faculty->id->value => $faculty]
        );

        if ($this->collection->count() !== count($faculties)) {
            throw new DomainException('FacultyIdが同じオブジェクトが存在しています。');
        }
    }

    public function count(): int
    {
        return $this->collection->count();
    }

    public function toArray(): array
    {
        return $this->collection->values()->map(
            fn (Faculty $faculty) => clone $faculty
        )->all();
    }

    public function isEmpty(): bool
    {
        return $this->collection->isEmpty();
    }
}
