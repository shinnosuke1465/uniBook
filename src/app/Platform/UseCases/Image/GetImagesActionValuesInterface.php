<?php

namespace App\Platform\UseCases\Image;

use App\Platform\Domains\Image\ImageIdList;

interface GetImagesActionValuesInterface
{
    public function getImageIdList(): ImageIdList;
}

