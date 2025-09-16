<?php

namespace App\Platform\UseCases\Image;

use App\Platform\Domains\Image\ImageId;
use App\Platform\Domains\Shared\String\String255;

interface CreateImageActionValuesInterface
{
    public function getPath(): String255;
    public function getType(): String255;
}

