<?php

namespace App\Exceptions;

use App\Exceptions\Constracts\WarningExceptionInterface;
use Exception;

class DomainException extends Exception implements WarningExceptionInterface
{
}
