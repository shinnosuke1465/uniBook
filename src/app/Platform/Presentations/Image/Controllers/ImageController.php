<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Image\Controllers;

use App\Platform\Presentations\Image\Requests\CreateImageRequest;
use App\Platform\Presentations\Image\Requests\GetImagesRequest;
use App\Platform\Presentations\Image\Requests\GetImageRequest;
use App\Platform\UseCases\Image\CreateImageAction;
use App\Platform\UseCases\Image\GetImagesAction;
use App\Platform\UseCases\Image\GetImageAction;
use Illuminate\Http\Response;

readonly class ImageController
{
    public function index(
        GetImagesRequest $request,
        GetImagesAction $action
    ): array {
        $dtos = $action($request);
        return GetImagesResponseBuilder::toArray($dtos);
    }

    public function show(
        GetImageRequest $request,
        GetImageAction $action
    ): array {
        $dto = $action($request);
        return GetImageResponseBuilder::toArray($dto);
    }

    public function store(
        CreateImageRequest $request,
        CreateImageAction $action
    ): Response {
        $action($request);
        return response()->noContent();
    }
}

