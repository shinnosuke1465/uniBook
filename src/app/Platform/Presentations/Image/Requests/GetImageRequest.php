<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Image\Requests;

use App\Exceptions\DomainException;
use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\Image\GetImageActionValuesInterface;
use App\Platform\Domains\Image\ImageId;

class GetImageRequest extends BaseRequest implements GetImageActionValuesInterface
{
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'string'
            ],
        ];
    }

    /**
     * @throws DomainException
     */
    public function getImageId(): ImageId
    {
        return new ImageId($this->input('id'));
    }
}

