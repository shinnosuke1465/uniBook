<?php

declare(strict_types=1);

namespace App\Platform\Domains\Image;

use App\Exceptions\DomainException;
use Illuminate\Support\Collection;

readonly class ImageList
{
    /**
     * @var Collection<string, Image>
     */
    private Collection $collection;

    /**
     * @param Image[] $images
     * @throws DomainException
     */
    public function __construct(array $images)
    {
        $this->collection = collect($images)->mapWithKeys(
            fn (Image $image) => [$image->id->value => $image]
        );

        if ($this->collection->count() !== count($images)) {
            throw new DomainException('ImageIdが同じオブジェクトが存在しています。');
        }
    }

    public function count(): int
    {
        return $this->collection->count();
    }

    public function toArray(): array
    {
        return $this->collection->values()->map(
            fn (Image $image) => clone $image
        )->all();
    }

    /**
     * @throws DomainException
     */
    public function getIds(): ImageIdList
    {
        return new ImageIdList($this->collection->map(
            fn (Image $image) => $image->id
        )->all());
    }

    public function isEmpty(): bool
    {
        return $this->collection->isEmpty();
    }
}
