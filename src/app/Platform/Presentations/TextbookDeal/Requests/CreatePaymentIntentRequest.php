<?php

declare(strict_types=1);

namespace App\Platform\Presentations\TextbookDeal\Requests;

use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\TextbookDeal\CreatePaymentIntentActionValuesInterface;

class CreatePaymentIntentRequest extends BaseRequest implements CreatePaymentIntentActionValuesInterface
{
    public function authorize(): bool
    {
        /** @var string $textbookId */
        $textbookId = $this->route('textbookId');

        /** @var \App\Models\Textbook $textbook */
        $textbook = \App\Models\Textbook::findOrFail($textbookId);
        $deal = $textbook->deal;

        // 教科書が既に取引中でないかチェック
        if ($deal !== null) {
            return false;
        }

        // ユーザーが自分の教科書を購入しようとしていないかチェック
        if ($this->user()->cannot('purchase-deal', $deal)) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
        ];
    }
}
