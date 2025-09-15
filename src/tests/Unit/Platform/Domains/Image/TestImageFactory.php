<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Image;

use App\Exceptions\DomainException;
use App\Platform\Domains\Image\Image;
use App\Platform\Domains\Image\ImageId;
use App\Platform\Domains\Shared\String\String255;

class TestImageFactory
{
    public static function create(
        ImageId $id = new ImageId(),
        String255 $path = new String255('test/path'),
        String255 $type = new String255('png'),
    ): Image {
        return new Image(
            $id,
            $path,
            $type,
        );
    }
}

