<?php

declare(strict_types=1);

namespace App\Platform\Presentations\User\Me\Controllers;

use App\Exceptions\DomainException;
use App\Platform\Presentations\User\Me\Requests\GetLikedTextbooksRequest;
use App\Platform\UseCases\User\Me\GetLikedTextbooksAction;

readonly class GetLikedTextbooksController
{
    /**
     * 認証済みユーザーがいいねした教科書一覧を取得
     *
     * @throws DomainException
     */
    public function index(
        GetLikedTextbooksRequest $request,
        GetLikedTextbooksAction $action
    ): array {
        $result = $action($request);

        return GetLikedTextbooksResponseBuilder::toArray($result);
    }
}