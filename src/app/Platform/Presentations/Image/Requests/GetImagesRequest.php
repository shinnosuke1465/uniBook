<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Image\Requests;

use App\Exceptions\DomainException;
use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\Image\GetImagesActionValuesInterface;
use App\Platform\Domains\Image\ImageId;
use App\Platform\Domains\Image\ImageIdList;

class GetImagesRequest extends BaseRequest implements GetImagesActionValuesInterface
{
    public function rules(): array
    {
        return [
            'ids' => [
                'array'
            ],
            'ids.*' => [
                'string'
            ],
        ];
    }

    /**
     * @throws DomainException
     */
    public function getImageIdList(): ImageIdList
    {
        $inputValue = $this->input('ids');
        if ($inputValue === null) {
            return new ImageIdList([]);
        }
        $imageIds =collect($inputValue)->map(
            fn (string $id) => new ImageId($id)
        )->all();
        return new ImageIdList($imageIds);
    }
}

