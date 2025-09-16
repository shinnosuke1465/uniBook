<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Exceptions\Constracts\WarningExceptionInterface;
use Exception;

class SrmsException extends Exception implements WarningExceptionInterface
{
}
