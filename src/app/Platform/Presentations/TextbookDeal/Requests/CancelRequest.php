<?php

declare(strict_types=1);

namespace App\Platform\Presentations\TextbookDeal\Requests;

use App\Platform\UseCases\TextbookDeal\CancelActionValuesInterface;

readonly class CancelRequest extends BaseRequest implements CancelActionValuesInterface
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

        // ユーザーが自分の教科書を出品キャンセルしようとしているかチェック
        if ($this->user()->cannot('cancel-deal', $deal)) {
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
        throw new \Illuminate\Auth\Access\AuthorizationException('出品キャンセルできないユーザーです。');
    }
}
