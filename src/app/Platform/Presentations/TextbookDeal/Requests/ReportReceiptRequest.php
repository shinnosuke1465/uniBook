<?php

declare(strict_types=1);

namespace App\Platform\Presentations\TextbookDeal\Requests;

use App\Platform\UseCases\TextbookDeal\ReportReceiptActionValuesInterface;
use App\Platform\Presentations\Shared\BaseRequest;
use App\Models\Textbook;

class ReportReceiptRequest extends BaseRequest implements ReportReceiptActionValuesInterface
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

        // ステータスがShipping（配送中）でない場合は認可拒否
        if ($deal->deal_status !== 'Shipping') {
            return false;
        }

        // ユーザーが購入者として受取報告しようとしているかチェック
        if ($this->user()->cannot('report-receipt-deal', $deal)) {
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
        throw new \Illuminate\Auth\Access\AuthorizationException('受取報告できないユーザーです。');
    }
}