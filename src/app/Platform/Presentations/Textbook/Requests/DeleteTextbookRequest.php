<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Textbook\Requests;

use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\Textbook\DeleteTextbookActionValuesInterface;

class DeleteTextbookRequest extends BaseRequest implements DeleteTextbookActionValuesInterface
{
    public function rules(): array
    {
        return [
        ];
    }
}