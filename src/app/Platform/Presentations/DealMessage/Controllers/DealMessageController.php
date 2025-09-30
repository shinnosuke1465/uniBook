<?php

declare(strict_types=1);

namespace App\Platform\Presentations\DealMessage\Controllers;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Platform\Presentations\DealMessage\Requests\CreateDealMessageRequest;
use App\Platform\UseCases\DealMessage\CreateDealMessageAction;
use Illuminate\Http\Response;

readonly class DealMessageController
{
    /**
     * @throws DomainException
     * @throws NotFoundException
     */
    public function store(
        CreateDealMessageRequest $request,
        CreateDealMessageAction $action,
        string $dealRoomId
    ): Response {
        $action($request, $dealRoomId);
        return response()->noContent();
    }
}