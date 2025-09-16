<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Faculty\Requests;

use App\Exceptions\DomainException;
use App\Platform\Domains\Shared\String\String255;
use App\Platform\Domains\University\UniversityId;
use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\Faculty\CreateFacultyActionValuesInterface;
use App\Platform\UseCases\User\CreateUserValuesInterface;
use Ramsey\Uuid\Type\Integer;

class CreateFacultyRequest extends BaseRequest implements CreateFacultyActionValuesInterface
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
            'university_id' => [
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

    /**
     * @throws DomainException
     */
    public function getUniversityId(): UniversityId
    {
        return new UniversityId($this->input('university_id'));
    }
}
