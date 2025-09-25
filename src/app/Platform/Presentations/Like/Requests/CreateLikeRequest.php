<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Like\Requests;

use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\Like\CreateLikeActionValuesInterface;

class CreateLikeRequest extends BaseRequest implements CreateLikeActionValuesInterface
{
    public function rules(): array
    {
        return [
            // Likeはtextフィールドがないため、バリデーションルールなし
        ];
    }
}