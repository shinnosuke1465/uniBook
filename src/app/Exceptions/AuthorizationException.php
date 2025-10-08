<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Exceptions\Constracts\WarningExceptionInterface;
use Exception;

class AuthorizationException  extends Exception implements WarningExceptionInterface
{
}
