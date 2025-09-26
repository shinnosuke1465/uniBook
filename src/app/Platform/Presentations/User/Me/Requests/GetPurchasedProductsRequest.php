<?php

declare(strict_types=1);

namespace App\Platform\Presentations\User\Me\Requests;

use App\Platform\UseCases\User\Me\GetPurchasedProductsActionValuesInterface;
use Illuminate\Foundation\Http\FormRequest;

class GetPurchasedProductsRequest extends FormRequest implements GetPurchasedProductsActionValuesInterface
{
    public function rules(): array
    {
        return [
        ];
    }
}
