<?php

declare(strict_types=1);

namespace App\Platform\Presentations\User\Me\Controllers;

use App\Exceptions\DomainException;
use App\Platform\Presentations\User\Me\Requests\GetListedTextbooksRequest;
use App\Platform\UseCases\User\Me\GetListedTextbooksAction;
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
}
