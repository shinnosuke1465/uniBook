<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Faculty\Controllers;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
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

    /**
     * @throws DomainException
     * @throws NotFoundException
     */
    public function show(
        GetFacultyRequest $request,
        GetFacultyAction $action,
        string $facultyIdString
    ): array {
        $dto = $action($request, $facultyIdString);

        return GetFacultyResponseBuilder::toArray($dto);
    }

    /**
     * @throws DomainException
     */
    public function store(
        CreateFacultyRequest $request,
        CreateFacultyAction $action
    ): Response {
        $action($request);

        return response()->noContent();
    }
}
