<?php

declare(strict_types=1);

namespace App\Platform\Presentations\DealMessage\Requests;

use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\DealMessage\CreateDealMessageActionValuesInterface;

class CreateDealMessageRequest extends BaseRequest implements CreateDealMessageActionValuesInterface
{
    public function rules(): array
    {
        return [
            'message' => [
                'required',
                'string',
            ],
        ];
    }

    public function getMessage(): Text
    {
        return new Text($this->input('message'));
    }
}