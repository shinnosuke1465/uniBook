<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Like\Requests;

use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\Like\DeleteLikeActionValuesInterface;

class DeleteLikeRequest extends BaseRequest implements DeleteLikeActionValuesInterface
{
    public function rules(): array
    {
        return [
        ];
    }
}
