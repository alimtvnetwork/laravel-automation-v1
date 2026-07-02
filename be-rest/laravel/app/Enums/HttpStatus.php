<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * HTTP status codes per spec/21-app/00-rest-general/spec.md §5.2.
 */
enum HttpStatus: int
{
    case Ok                  = 200;
    case Created             = 201;
    case NoContent           = 204;
    case BadRequest          = 400;
    case Unauthorized        = 401;
    case Forbidden           = 403;
    case NotFound            = 404;
    case PayloadTooLarge     = 413;
    case UnsupportedMedia    = 415;
    case UnprocessableEntity = 422;
    case ServerError         = 500;
}
