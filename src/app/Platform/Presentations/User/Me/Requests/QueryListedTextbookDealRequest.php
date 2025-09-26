<?php

declare(strict_types=1);

namespace App\Platform\Presentations\User\Me\Requests;

use App\Platform\UseCases\User\Me\QueryListedTextbookDealActionValues;
use Illuminate\Foundation\Http\FormRequest;

class QueryListedTextbookDealRequest extends FormRequest implements QueryListedTextbookDealActionValues
{
    /**
     * バリデーションルール
     * URLパラメータから教科書IDを取得するため空
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // パラメータはURLから取得
        ];
    }
}