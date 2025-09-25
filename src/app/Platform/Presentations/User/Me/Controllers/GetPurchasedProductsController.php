<?php

declare(strict_types=1);

namespace App\Platform\Presentations\User\Me\Controllers;

use App\Exceptions\DomainException;
use App\Platform\Presentations\User\Me\Requests\GetPurchasedProductsRequest;
use App\Platform\UseCases\User\Me\GetPurchasedProductsAction;
use App\Platform\UseCases\User\Me\GetPurchasedProductsActionValuesInterface;

readonly class GetPurchasedProductsController
{
    /**
     * @throws DomainException
     */
    public function index(
        GetPurchasedProductsRequest $request,
        GetPurchasedProductsAction $action
    ): array {
        $result = $action($request);

        return GetPurchasedProductsResponseBuilder::toArray($result);
    }
}
