<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Like\Controllers;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Platform\Presentations\Like\Requests\CreateLikeRequest;
use Illuminate\Http\Response;
use App\Platform\UseCases\Like\CreateLikeAction;

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
}
