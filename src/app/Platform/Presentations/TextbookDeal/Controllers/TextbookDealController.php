<?php

declare(strict_types=1);

namespace App\Platform\Presentations\TextbookDeal\Controllers;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Platform\Presentations\TextbookDeal\Requests\CancelRequest;
use App\Platform\Presentations\TextbookDeal\Requests\CreatePaymentIntentRequest;
use App\Platform\Presentations\TextbookDeal\Requests\ReportDeliveryRequest;
use App\Platform\Presentations\TextbookDeal\Requests\VerifyPaymentIntentRequest;
use App\Platform\UseCases\TextbookDeal\CreatePaymentIntentAction;
use App\Platform\UseCases\TextbookDeal\ReportDeliveryAction;
use App\Platform\UseCases\TextbookDeal\VerifyPaymentIntentAction;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use App\Platform\UseCases\TextbookDeal\CancelAction;

readonly class TextbookDealController
{
    /**
     * @throws DomainException
     * @throws NotFoundException
     * @throws AuthorizationException
     */
    public function createPaymentIntent(
        CreatePaymentIntentRequest $request,
        CreatePaymentIntentAction $action,
        string $textbookId
    ): array {
        $dtos = $action($request, $textbookId);
        return CreatePaymentIntentResponseBuilder::toArray($dtos);
    }

    /**
     * 商品支払いインテント確認API
     * @throws DomainException
     * @throws NotFoundException
     * @throws AuthorizationException
     */
    public function verifyPaymentIntent(
        VerifyPaymentIntentRequest $request,
        VerifyPaymentIntentAction $action,
        string $textbookId
    ):Response {
        $action($request, $textbookId);
        return response()->noContent();
    }

    /**
     * 出品キャンセルAPI
     * @throws DomainException
     * @throws NotFoundException
     * @throws AuthorizationException
     */
    public function cancel(
        CancelRequest $request,
        CancelAction $action,
        string $textbookId
    ):Response {
        $action($request, $textbookId);
        return response()->noContent();
    }

    /**
     * 配送報告API
     * @throws DomainException
     * @throws NotFoundException
     * @throws AuthorizationException
     */
    public function reportDelivery(
        ReportDeliveryRequest $request,
        ReportDeliveryAction $action,
        string $textbookId
    ): Response {
        $action($request, $textbookId);
        return response()->noContent();
    }
}
