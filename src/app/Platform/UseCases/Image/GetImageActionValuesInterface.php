<?php

namespace App\Platform\UseCases\Image;

use App\Platform\Domains\Image\ImageId;

interface GetImageActionValuesInterface
{
    public function getImageId(): ImageId;
}

