<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Faculty\Requests;

use App\Exceptions\DomainException;
use App\Platform\Domains\University\UniversityId;
use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\Faculty\GetFacultiesActionValuesInterface;

class GetFacultiesRequest extends BaseRequest implements GetFacultiesActionValuesInterface
{
    public function rules(): array
    {
        return [
            'university_id' => [
                'string',
            ],
        ];
    }

    /**
     * @throws DomainException
     */
    public function getUniversityId(): UniversityId
    {
        return new UniversityId($this->input('university_id'));
    }
}
