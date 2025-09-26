<?php

declare(strict_types=1);

namespace App\Platform\Presentations\User\Me\Controllers;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Platform\Presentations\User\Me\Requests\GetPurchasedProductsRequest;
use App\Platform\Presentations\User\Me\Requests\QueryPurchasedTextbookDealRequest;
use App\Platform\UseCases\User\Me\GetPurchasedProductsAction;
use App\Platform\UseCases\User\Me\GetPurchasedProductsActionValuesInterface;
use App\Platform\UseCases\User\Me\QueryPurchasedTextbookDealAction;
use App\Platform\UseCases\User\Me\QueryPurchasedTextbookDealActionValues;
use Illuminate\Http\JsonResponse;

readonly class GetPurchasedProductsController
{
    /**
     * @throws DomainException
     */
    public function index(
        GetPurchasedProductsRequest $request,
        GetPurchasedProductsAction $action
    ): array {
        $result = $action($request);

        return GetPurchasedProductsResponseBuilder::toArray($result);
    }

    /**
     * 購入商品の取引詳細を取得
     *
     * @throws DomainException
     * @throws NotFoundException
     */
    public function show(
        QueryPurchasedTextbookDealRequest $request,
        QueryPurchasedTextbookDealAction $action,
        string $textbookIdString
    ): JsonResponse {
        $result = $action($request, $textbookIdString);

        $responseData = GetPurchasedTextbookDealResponseBuilder::toArray($result);

        return response()->json($responseData);
    }
}
