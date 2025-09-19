<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Textbook\Requests;

use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\Textbook\GetTextbooksActionValuesInterface;

class GetTextbooksRequest extends BaseRequest implements GetTextbooksActionValuesInterface
{
    public function rules(): array
    {
        return [
        ];
    }
}