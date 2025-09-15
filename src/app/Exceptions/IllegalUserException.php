<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Exceptions\Constracts\WarningExceptionInterface;
use EXception;

class IllegalUserException extends Exception implements WarningExceptionInterface
{
}
