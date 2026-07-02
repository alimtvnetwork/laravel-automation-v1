<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\ErrorCode;
use App\Enums\HttpStatus;
use RuntimeException;

/**
 * Base typed API exception mapped to the standard envelope
 * per spec/03-error-manage/03-error-code-registry.
 */
abstract class ApiException extends RuntimeException
{
    abstract public function code(): ErrorCode;

    abstract public function status(): HttpStatus;
}
