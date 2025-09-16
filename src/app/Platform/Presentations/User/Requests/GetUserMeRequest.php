<?php

declare(strict_types=1);

namespace App\Platform\Presentations\User\Requests;

use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\User\GetUserMeActionValuesInterface;

class GetUserMeRequest extends BaseRequest implements GetUserMeActionValuesInterface
{
    public function rules(): array
    {
        return [
        ];
    }
}
