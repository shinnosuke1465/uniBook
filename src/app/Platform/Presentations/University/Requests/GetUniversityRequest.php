<?php

declare(strict_types=1);

namespace App\Platform\Presentations\University\Requests;

use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\University\GetUniversityActionValuesInterface;
use App\Platform\Domains\University\UniversityId;
use App\Exceptions\DomainException;

class GetUniversityRequest extends BaseRequest implements GetUniversityActionValuesInterface
{
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'string',
            ],
        ];
    }

    /**
     * @throws DomainException
     */
    public function getUniversityId(): UniversityId
    {
        return new UniversityId($this->input('id'));
    }
}
