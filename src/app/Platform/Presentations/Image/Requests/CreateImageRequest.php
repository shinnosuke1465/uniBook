<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Image\Requests;

use App\Exceptions\DomainException;
use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\Image\CreateImageActionValuesInterface;
use App\Platform\Domains\Shared\String\String255;

class CreateImageRequest extends BaseRequest implements CreateImageActionValuesInterface
{
    public function rules(): array
    {
        return [
            'path' => [
                'required',
                'string'
            ],
            'type' => [
                'required',
                'string'
            ],
        ];
    }

    /**
     * @throws DomainException
     */
    public function getPath(): String255
    {
        return new String255($this->input('path'));
    }

    /**
     * @throws DomainException
     */
    public function getType(): String255
    {
        return new String255($this->input('type'));
    }
}

