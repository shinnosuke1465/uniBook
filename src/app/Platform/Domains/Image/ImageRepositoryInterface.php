<?php

namespace App\Platform\Domains\Image;

interface ImageRepositoryInterface
{
    public function findById(ImageId $imageId): ?Image;

    public function findByIds(ImageIdList $imageIdList): ImageList;

    public function insert(Image $image): void;
}

