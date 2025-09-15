<?php

namespace App\Platform\Infrastructures\Image;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Platform\Domains\Image\ImageIdList;
use App\Platform\Domains\Image\ImageList;
use App\Platform\Domains\Image\ImageRepositoryInterface;
use App\Platform\Domains\Image\Image;
use App\Platform\Domains\Image\ImageId;
use App\Models\Image as ImageDB;

class ImageRepository implements ImageRepositoryInterface
{
    /**
     * @throws DomainException
     */
    public function findById(
        ImageId $imageId
    ): ?Image{
        $imageModel = ImageDB::find($imageId->value);

        if ($imageModel === null) {
            return null;
        }

        return ImageFactory::create($imageModel);
    }

    /**
     * @throws DomainException
     */
    public function findByIds(
        ImageIdList $imageIdList
    ): ImageList {
        $images = ImageDB::query()
            ->findMany($imageIdList->toStringArray())
            ->map(
                fn ($imageModel) => ImageFactory::create($imageModel)
            )->all();

        return new ImageList($images);
    }

    /**
     * @throws DuplicateKeyException
     */
    public function insert(
        Image $image
    ): void {
        if ($this->hasDuplicate($image->id)){
            throw new DuplicateKeyException('画像が重複しています。');
        }

        ImageDB::create([
            'id' => $image->id->value,
            'path' => $image->path->value,
            'type' => $image->type->value,
        ]);
    }

    private function hasDuplicate(ImageId $imageId): bool
    {
        return ImageDB::find($imageId->value) !== null;
    }
}

