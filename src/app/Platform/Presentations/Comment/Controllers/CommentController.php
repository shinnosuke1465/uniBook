<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Comment\Controllers;

use App\Platform\Presentations\Comment\Requests\CreateCommentRequest;
use Illuminate\Http\Response;
use App\Platform\UseCases\Comment\CreateCommentAction;

readonly class CommentController
{
    public function store(
        CreateCommentRequest $request,
        CreateCommentAction $action,
        string $id
    ): Response {
        $action($request, $id);
        return response()->noContent();
    }
}
