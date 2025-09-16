<?php

declare(strict_types=1);

namespace App\Platform\Presentations\University\Requests;

use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\University\GetUniversityActionValuesInterface;

class GetUniversityRequest extends BaseRequest implements GetUniversityActionValuesInterface
{
    public function rules(): array
    {
        return [
        ];
    }
}

