<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Faculty\Requests;

use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\Faculty\GetFacultyActionValuesInterface;

class GetFacultyRequest extends BaseRequest implements GetFacultyActionValuesInterface
{
    public function rules(): array
    {
        return [
        ];
    }
}
