<?php

declare(strict_types=1);

namespace App\Platform\Presentations\User\Me\Requests;

use App\Platform\UseCases\User\Me\GetListedTextbooksActionValuesInterface;
use Illuminate\Foundation\Http\FormRequest;

class GetListedTextbooksRequest extends FormRequest implements GetListedTextbooksActionValuesInterface
{
    /**
     * バリデーションルール
     * 出品教科書一覧取得にはパラメータが不要のため空
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
        ];
    }
}
