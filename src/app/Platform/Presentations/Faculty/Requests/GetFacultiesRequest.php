<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Faculty\Requests;

use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\Faculty\GetFacultiesActionValuesInterface;

class GetFacultiesRequest extends BaseRequest implements GetFacultiesActionValuesInterface
{
    public function rules(): array
    {
        return [
        ];
    }
}
