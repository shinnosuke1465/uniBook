<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Image\Requests;

use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\Image\CreateImageActionValuesInterface;
use Illuminate\Http\UploadedFile;

class CreateImageRequest extends BaseRequest implements CreateImageActionValuesInterface
{
    public function rules(): array
    {
        return [
            'image' => [
                'required',
                'file',
                'mimes:jpeg,jpg,png,gif',
                'max:2048'
            ],
        ];
    }

    public function getImageFile(): UploadedFile
    {
        return $this->file('image');
    }
}

