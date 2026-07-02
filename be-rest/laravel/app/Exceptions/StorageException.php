<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\ErrorCode;
use App\Enums\HttpStatus;

final class StorageException extends ApiException
{
    public function code(): ErrorCode
    {
        return ErrorCode::StorageError;
    }

    public function status(): HttpStatus
    {
        return HttpStatus::ServerError;
    }
}
