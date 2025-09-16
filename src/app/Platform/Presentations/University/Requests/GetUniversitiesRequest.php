<?php

declare(strict_types=1);

namespace App\Platform\Presentations\University\Requests;

use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\University\GetUniversitiesActionValuesInterface;

class GetUniversitiesRequest extends BaseRequest implements GetUniversitiesActionValuesInterface
{
    public function rules(): array
    {
        return [
        ];
    }
}

