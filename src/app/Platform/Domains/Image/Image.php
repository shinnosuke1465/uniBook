<?php

declare(strict_types=1);

namespace App\Platform\Domains\Image;

use App\Platform\Domains\Shared\String\String255;

readonly class Image
{
    public function __construct(
        public ImageId $id,
        public String255 $path,
        public String255 $type,
    ){
    }

    public static function create(String255 $path, String255 $type): self
    {
        return new self(new ImageId(), $path, $type);
    }
}
