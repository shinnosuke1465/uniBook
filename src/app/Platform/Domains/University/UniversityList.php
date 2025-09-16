<?php

declare(strict_types=1);

namespace App\Platform\Domains\University;

use Illuminate\Support\Collection;
use App\Exceptions\DomainException;

readonly class UniversityList
{
    /**
     * @var Collection <int, University>
     */
    private Collection $collection;

    /**
     * @param University[] $universities
     * @throws DomainException
     */
    public function __construct(array $universities)
    {
        $this->collection = collect($universities)->mapWithKeys(
            fn (University $university) => [$university->id->value => $university]
        );

        if ($this->collection->count() !== count($universities)) {
            throw new DomainException('UniversityIdが同じオブジェクトが存在しています。');
        }
    }

    public function count(): int
    {
        return $this->collection->count();
    }

    public function toArray(): array
    {
        return $this->collection->values()->map(
            fn (University $university) => clone $university
        )->all();
    }

    public function isEmpty(): bool
    {
        return $this->collection->isEmpty();
    }
}

