<?php

namespace App\Platform\UseCases\DealMessage;

use App\Platform\Domains\Shared\Text\Text;

interface CreateDealMessageActionValuesInterface
{
    public function getMessage(): Text;
}