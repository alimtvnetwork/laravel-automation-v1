<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * HTTP status codes used by the unified response envelope.
 * Per spec/21-app/00-rest-general/spec.md.
 */
enum HttpStatus: int
{
    case Ok                  = 200;
    case Created             = 201;
    case BadRequest          = 400;
    case Unauthorized        = 401;
    case Forbidden           = 403;
    case NotFound            = 404;
    case UnprocessableEntity = 422;
    case ServerError         = 500;

    public function isEqual(self $other): bool
    {
        return $this === $other;
    }
}
