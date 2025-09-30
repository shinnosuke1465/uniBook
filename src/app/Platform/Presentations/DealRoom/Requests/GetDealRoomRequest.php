<?php

declare(strict_types=1);

namespace App\Platform\Presentations\DealRoom\Requests;

use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\DealRoom\GetDealRoomActionValuesInterface;

class GetDealRoomRequest extends BaseRequest implements GetDealRoomActionValuesInterface
{
    public function rules(): array
    {
        return [
        ];
    }
}