<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Textbook\Requests;

use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\Textbook\GetTextbookActionValuesInterface;

class GetTextbookRequest extends BaseRequest implements GetTextbookActionValuesInterface
{
    public function rules(): array
    {
        return [
        ];
    }
}