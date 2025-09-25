<?php

declare(strict_types=1);

namespace App\Platform\Presentations\Comment\Requests;

use App\Platform\Domains\Shared\Text\Text;
use App\Platform\Presentations\Shared\BaseRequest;
use App\Platform\UseCases\Comment\CreateCommentActionValuesInterface;

class CreateCommentRequest extends BaseRequest implements CreateCommentActionValuesInterface
{
    public function rules(): array
    {
        return [
            'text' => [
                'required',
                'string',
            ],
        ];
    }

    public function getText(): Text
    {
        return new Text($this->input('text'));
    }
}
