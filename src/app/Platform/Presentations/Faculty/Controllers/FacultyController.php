<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Faculty\Controllers;

use App\Platform\Presentations\Faculty\Requests\CreateFacultyRequest;
use App\Platform\Presentations\Faculty\Requests\GetFacultiesRequest;
use App\Platform\Presentations\Faculty\Requests\GetFacultyRequest;
use App\Platform\UseCases\Faculty\CreateFacultyAction;
use App\Platform\UseCases\Faculty\GetFacultiesAction;
use App\Platform\UseCases\Faculty\GetFacultyAction;
use Illuminate\Http\Response;

readonly class FacultyController
{
    public function index(
        GetFacultiesRequest $request,
        GetFacultiesAction $action
    ): array {
        $dtos = $action($request);

        return GetFacultiesResponseBuilder::toArray($dtos);
    }

    public function show(
        GetFacultyRequest $request,
        GetFacultyAction $action
    ): array {
        $dto = $action($request);

        return GetFacultyResponseBuilder::toArray($dto);
    }

    public function store(
        CreateFacultyRequest $request,
        CreateFacultyAction $action
    ): Response {
        $action($request);

        return response()->noContent();
    }
}
