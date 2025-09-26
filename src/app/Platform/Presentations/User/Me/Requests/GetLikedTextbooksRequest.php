<?php

declare(strict_types=1);

namespace App\Platform\Presentations\User\Me\Requests;

use App\Platform\UseCases\User\Me\GetLikedTextbooksActionValuesInterface;
use Illuminate\Foundation\Http\FormRequest;

class GetLikedTextbooksRequest extends FormRequest implements GetLikedTextbooksActionValuesInterface
{
    public function rules(): array
    {
        return [
        ];
    }
}