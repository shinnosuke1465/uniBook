<?php

namespace App\Platform\UseCases\Comment;

use App\Platform\Domains\Shared\Text\Text;

interface CreateCommentActionValuesInterface
{
    public function getText(): Text;
}
