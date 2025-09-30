<?php

declare(strict_types=1);

namespace App\Platform\Presentations\DealRoom\Controllers;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Platform\Presentations\DealRoom\Requests\GetDealRoomsRequest;
use App\Platform\Presentations\DealRoom\Requests\GetDealRoomRequest;
use App\Platform\UseCases\DealRoom\GetDealRoomsAction;
use App\Platform\UseCases\DealRoom\GetDealRoomAction;

readonly class DealRoomController
{
    /**
     * ユーザーが参加している取引ルーム一覧を取得
     */
    public function index(
        GetDealRoomsRequest $request,
        GetDealRoomsAction $action
    ): array {
        $dtos = $action($request);
        return GetDealRoomsResponseBuilder::toArray($dtos);
    }

    /**
     * 指定された取引ルームの詳細を取得
     *
     * @throws DomainException
     * @throws NotFoundException
     */
    public function show(
        GetDealRoomRequest $request,
        GetDealRoomAction $action,
        string $dealRoomIdString
    ): array {
        $dto = $action($request, $dealRoomIdString);
        return GetDealRoomResponseBuilder::toArray($dto);
    }
}
