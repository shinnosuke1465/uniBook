<?php

declare(strict_types=1);

namespace App\Platform\Presentations\University\Controllers;

use App\Platform\Presentations\University\Requests\CreateUniversityRequest;
use App\Platform\Presentations\University\Requests\GetUniversitiesRequest;
use App\Platform\Presentations\University\Requests\GetUniversityRequest;
use App\Platform\UseCases\University\CreateUniversityAction;
use App\Platform\UseCases\University\GetUniversitiesAction;
use App\Platform\UseCases\University\GetUniversityAction;
use Illuminate\Http\Response;

readonly class UniversityController
{
    public function index(
        GetUniversitiesRequest $request,
        GetUniversitiesAction $action
    ): array {
        $dtos = $action($request);
        return GetUniversitiesResponseBuilder::toArray($dtos);
    }

    public function show(
        GetUniversityRequest $request,
        GetUniversityAction $action,
        string $universityIdString
    ): array {
        $dto = $action($request, $universityIdString);
        return GetUniversityResponseBuilder::toArray($dto);
    }

    public function store(
        CreateUniversityRequest $request,
        CreateUniversityAction $action
    ): Response {
        $action($request);
        return response()->noContent();
    }
}
