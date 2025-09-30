<?php

declare(strict_types=1);

namespace App\Platform\Presentations\TextbookDeal\Requests;

use App\Platform\UseCases\TextbookDeal\ReportDeliveryActionValuesInterface;
use App\Platform\Presentations\Shared\BaseRequest;
use App\Models\Textbook;

class ReportDeliveryRequest extends BaseRequest implements ReportDeliveryActionValuesInterface
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

        // ステータスがPurchased（購入済み）でない場合は認可拒否
        if ($deal->deal_status !== 'Purchased') {
            return false;
        }

        // ユーザーが自分の教科書を配送報告しようとしているかチェック
        if ($this->user()->cannot('report-delivery-deal', $deal)) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
        ];
    }

    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException('配送報告できないユーザーです。');
    }
}