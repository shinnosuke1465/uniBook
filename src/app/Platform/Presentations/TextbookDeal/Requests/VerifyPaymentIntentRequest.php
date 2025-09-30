<?php

declare(strict_types=1);

namespace App\Platform\Presentations\TextbookDeal\Requests;

use App\Exceptions\DomainException;
use App\Models\Textbook;
use App\Platform\Domains\PaymentIntent\ClientSecret;
use App\Platform\Domains\PaymentIntent\PaymentIntentId;
use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\TextbookDeal\VerifyPaymentIntentActionValuesInterface;

class VerifyPaymentIntentRequest extends BaseRequest implements VerifyPaymentIntentActionValuesInterface
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
            'payment_intent_id' => [
                'required',
                'string',
            ],
        ];
    }

    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException('教科書を購入できないユーザーです。');
    }

    /**
     * @throws DomainException
     */
    public function getPaymentIntentId(): PaymentIntentId
    {
        return new PaymentIntentId($this->input('payment_intent_id'));
    }

    public function getClientSecret(): ClientSecret
    {
        return new ClientSecret($this->input('client_secret'));
    }
}
