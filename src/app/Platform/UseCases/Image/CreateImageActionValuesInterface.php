<?php

namespace App\Platform\UseCases\Image;

use Illuminate\Http\UploadedFile;

interface CreateImageActionValuesInterface
{
    public function getImageFile(): UploadedFile;
}

