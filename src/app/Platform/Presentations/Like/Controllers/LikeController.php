<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Like\Controllers;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Platform\Presentations\Like\Requests\CreateLikeRequest;
use App\Platform\Presentations\Like\Requests\DeleteLikeRequest;
use Illuminate\Http\Response;
use App\Platform\UseCases\Like\CreateLikeAction;
use App\Platform\UseCases\Like\DeleteLikeAction;

readonly class LikeController
{
    /**
     * @throws DomainException
     * @throws NotFoundException
     */
    public function store(
        CreateLikeRequest $request,
        CreateLikeAction $action,
        string $id
    ): Response {
        $action($request, $id);
        return response()->noContent();
    }

    /**
     * @throws DomainException
     * @throws NotFoundException
     */
    public function delete(
        DeleteLikeRequest $request,
        DeleteLikeAction $action,
        string $id
    ): Response {
        $action($request, $id);
        return response()->noContent();
    }
}
