<?php

declare(strict_types=1);

namespace App\Platform\Domains\Image;

use App\Exceptions\DomainException;
use Illuminate\Support\Collection;

readonly class ImageIdList
{
    /**
     * @var Collection <string, ImageId>
     */
    private Collection $collection;

    /**
     * @param ImageId[] $imageIds
     * @throws DomainException
     */
    public function __construct(array $imageIds)
    {
        $this->collection = collect($imageIds)
            ->mapWithKeys(
                fn (ImageId $imageId) => [$imageId->value => $imageId]
            )
            ->sortKeys();

        if ($this->collection->count() !== count($imageIds)) {
            throw new DomainException('ImageIdが同じオブジェクトが存在しています。');
        }
    }

    public function equals(self $imageIdList): bool
    {
        $thisImageIds = $this->toArray();
        $inputImageIds = $imageIdList->toArray();

        if (count($thisImageIds) !== count($inputImageIds)) {
            return false;
        }

        for ($i = 0; $i < count($thisImageIds); $i++) {
            if (!$thisImageIds[$i]->equals($inputImageIds[$i])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string[]
     */
    public function toStringArray(): array
    {
        return $this->collection->map(
            fn (ImageId $imageId) => $imageId->value
        )->values()->all();
    }

    /**
     * @return ImageId[]
     */
    public function toArray(): array
    {
        return $this->collection->values()->map(
            fn (ImageId $imageId) => clone $imageId
        )->all();
    }
}
