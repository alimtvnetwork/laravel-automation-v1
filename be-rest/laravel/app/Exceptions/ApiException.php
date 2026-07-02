<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

/**
 * Base typed API exception. Concrete subclasses map to error codes
 * per spec/03-error-manage/03-error-code-registry.
 */
abstract class ApiException extends RuntimeException
{
    abstract public function errorCode(): int;

    abstract public function httpStatus(): int;
}
