<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Image\Controllers;

use App\Exceptions\DomainException;
use App\Platform\Presentations\Image\Requests\CreateImageRequest;
use App\Platform\Presentations\Image\Requests\GetImagesRequest;
use App\Platform\Presentations\Image\Requests\GetImageRequest;
use App\Platform\UseCases\Image\CreateImageAction;
use App\Platform\UseCases\Image\GetImagesAction;
use App\Platform\UseCases\Image\GetImageAction;
use Illuminate\Http\Response;
use Throwable;

readonly class ImageController
{
    /**
     * @return array<int, array<string, mixed>>
     * @throws Throwable
     */
    public function index(
        GetImagesRequest $request,
        GetImagesAction $action
    ): array {
        $dtos = $action($request);
        return GetImagesResponseBuilder::toArray($dtos);
    }

    /**
     * @return array<string, mixed>
     * @throws Throwable
     */
    public function show(
        GetImageRequest $request,
        GetImageAction $action,
        string $imageIdString
    ): array {
        $dto = $action($request, $imageIdString);
        return GetImageResponseBuilder::toArray($dto);
    }

    /**
     * @throws DomainException
     * @throws Throwable
     */
    public function store(
        CreateImageRequest $request,
        CreateImageAction $action
    ): Response {
        $action($request);
        return response()->noContent();
    }
}

