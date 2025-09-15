<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\Image;

use App\Exceptions\DomainException;
use App\Platform\Domains\Image\Image;
use App\Platform\Domains\Image\ImageId;
use App\Platform\Domains\Shared\String\String255;
use App\Models\Image as ImageDB;

readonly class ImageFactory
{
    /**
     * @throws DomainException
     */
    public static function create(
        ImageDB $imageDB,
    ): Image {
        return new Image(
            new ImageId($imageDB->id),
            new String255($imageDB->path),
            new String255($imageDB->type),
        );
    }
}

