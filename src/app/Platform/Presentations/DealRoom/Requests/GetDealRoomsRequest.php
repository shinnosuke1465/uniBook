<?php

declare(strict_types=1);

namespace App\Platform\Presentations\DealRoom\Requests;

use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\DealRoom\GetDealRoomsActionValuesInterface;

class GetDealRoomsRequest extends BaseRequest implements GetDealRoomsActionValuesInterface
{
    public function rules(): array
    {
        return [
        ];
    }
}