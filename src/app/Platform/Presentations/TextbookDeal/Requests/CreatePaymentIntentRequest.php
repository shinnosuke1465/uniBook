<?php

declare(strict_types=1);

namespace App\Platform\Presentations\TextbookDeal\Requests;

use App\Models\Textbook;
use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\TextbookDeal\CreatePaymentIntentActionValuesInterface;

class CreatePaymentIntentRequest extends BaseRequest implements CreatePaymentIntentActionValuesInterface
{
    public function authorize(): bool
    {
        /** @var string $textbookId */
        $textbookId = $this->route('textbookId');

        /** @var Textbook $textbook */
        $textbook = Textbook::findOrFail($textbookId);
        $deal = $textbook->deal;

        // 取引が存在しない場合は認可拒否
        if ($deal === null) {
            return false;
        }

        // ステータスがListing（出品中）でない場合は認可拒否
        if ($deal->deal_status !== 'Listing') {
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
