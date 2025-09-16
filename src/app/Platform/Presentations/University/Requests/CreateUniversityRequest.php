<?php

declare(strict_types=1);

namespace App\Platform\Presentations\University\Requests;

use App\Exceptions\DomainException;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\University\CreateUniversityActionValuesInterface;

class CreateUniversityRequest extends BaseRequest implements CreateUniversityActionValuesInterface
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
            ],
        ];
    }

    /**
     * @throws DomainException
     */
    public function getName(): String255
    {
        return new String255($this->input('name'));
    }
}
