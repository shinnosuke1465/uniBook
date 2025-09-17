<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Faculty\Requests;

use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\Faculty\GetFacultyActionValuesInterface;
use App\Platform\Domains\Faculty\FacultyId;
use App\Exceptions\DomainException;

class GetFacultyRequest extends BaseRequest implements GetFacultyActionValuesInterface
{
    public function rules(): array
    {
        return [
        ];
    }
//    public function getFacultyId(): FacultyId
//    {
//        return new FacultyId($this->input('id'));
//    }
}
