<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Textbook\Controllers;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Platform\Presentations\Textbook\Requests\CreateTextbookRequest;
use App\Platform\Presentations\Textbook\Requests\GetTextbooksRequest;
use App\Platform\Presentations\Textbook\Requests\GetTextbookRequest;
use App\Platform\Presentations\Textbook\Requests\UpdateTextbookRequest;
use App\Platform\Presentations\Textbook\Requests\DeleteTextbookRequest;
use App\Platform\UseCases\Textbook\CreateTextbookAction;
use App\Platform\UseCases\Textbook\GetTextbooksAction;
use App\Platform\UseCases\Textbook\GetTextbookAction;
use App\Platform\UseCases\Textbook\UpdateTextbookAction;
use App\Platform\UseCases\Textbook\DeleteTextbookAction;
use Illuminate\Http\Response;

readonly class TextbookController
{
    public function index(
        GetTextbooksRequest $request,
        GetTextbooksAction $action
    ): array {
        $dtos = $action($request);
        return GetTextbooksResponseBuilder::toArray($dtos);
    }

    /**
     * @throws DomainException
     * @throws NotFoundException
     */
    public function show(
        GetTextbookRequest $request,
        GetTextbookAction $action,
        string $textbookIdString
    ): array {
        $dto = $action($request, $textbookIdString);
        return GetTextbookResponseBuilder::toArray($dto);
    }

    /**
     * @throws DomainException
     */
    public function store(
        CreateTextbookRequest $request,
        CreateTextbookAction $action
    ): Response {
        $action($request);
        return response()->noContent();
    }

    /**
     * @throws DomainException
     * @throws NotFoundException
     */
    public function update(
        UpdateTextbookRequest $request,
        UpdateTextbookAction $action,
        string $textbookIdString
    ): Response {
        $action($request, $textbookIdString);
        return response()->noContent();
    }

    /**
     * @throws NotFoundException
     * @throws DomainException
     */
    public function destroy(
        DeleteTextbookRequest $request,
        DeleteTextbookAction $action,
        string $textbookIdString
    ): Response {
        $action($request, $textbookIdString);
        return response()->noContent();
    }
}
