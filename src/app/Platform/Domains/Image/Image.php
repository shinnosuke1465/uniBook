<?php

declare(strict_types=1);

namespace App\Platform\Domains\Image;

use App\Platform\Domains\Shared\String\String255;

readonly class Image
{
    public function __construct(
        public ImageId $id,
        public String255 $path
    ){
    }

    public static function create(String255 $path): self
    {
        return new self(new ImageId(), $path);
    }
}
