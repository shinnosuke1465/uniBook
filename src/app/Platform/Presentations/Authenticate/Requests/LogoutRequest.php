<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Authenticate\Requests;

use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\Authenticate\LogoutActionValuesInterface;

class LogoutRequest extends BaseRequest implements LogoutActionValuesInterface
{
    public function rules(): array
    {
        return [
        ];
    }
}
