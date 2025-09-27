<?php

declare(strict_types=1);

namespace App\Platform\Presentations\TextbookDeal\Controllers;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Platform\Presentations\TextbookDeal\Requests\CreatePaymentIntentRequest;
use App\Platform\UseCases\TextbookDeal\CreatePaymentIntentAction;
use Illuminate\Auth\Access\AuthorizationException;

readonly class TextbookDealController
{
    /**
     * @throws DomainException
     * @throws NotFoundException
     * @throws AuthorizationException
     */
    public function createPaymentIntent(
        CreatePaymentIntentRequest $request,
        CreatePaymentIntentAction $action,
        string $textbookId
    ): array {
        $dtos = $action($request, $textbookId);
        return createPaymentIntentResponseBuilder::toArray($dtos);
    }
}
