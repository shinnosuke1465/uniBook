<?php

declare(strict_types=1);

namespace App\Platform\Presentations\User\Me\Controllers;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Platform\Presentations\User\Me\Requests\GetListedTextbooksRequest;
use App\Platform\Presentations\User\Me\Requests\QueryListedTextbookDealRequest;
use App\Platform\UseCases\User\Me\GetListedTextbooksAction;
use App\Platform\UseCases\User\Me\QueryListedTextbookDealAction;
use Illuminate\Http\JsonResponse;

readonly class GetListedTextbooksController
{
    /**
     * 認証済みユーザーの出品教科書一覧を取得
     *
     * @throws DomainException
     */
    public function index(
        GetListedTextbooksRequest $request,
        GetListedTextbooksAction $action
    ): array {
        $result = $action($request);

        return GetListedTextbooksResponseBuilder::toArray($result);
    }

    /**
     * 出品商品の取引詳細を取得
     *
     * @throws DomainException
     * @throws NotFoundException
     */
    public function show(
        QueryListedTextbookDealRequest $request,
        QueryListedTextbookDealAction $action,
        string $textbookIdString
    ): array {
        $result = $action($request, $textbookIdString);

        return GetListedTextbookDealResponseBuilder::toArray($result);
    }
}
