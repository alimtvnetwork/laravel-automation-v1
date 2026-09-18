<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Machine-readable error codes per 02-spec/21-app/00-rest-general/spec.md §4.2.
 */
enum ErrorCode: string
{
    case ValidationError      = 'VALIDATION_ERROR';
    case UnsupportedMediaType = 'UNSUPPORTED_MEDIA_TYPE';
    case PayloadTooLarge      = 'PAYLOAD_TOO_LARGE';
    case Unauthorized         = 'UNAUTHORIZED';
    case Forbidden            = 'FORBIDDEN';
    case NotFound             = 'NOT_FOUND';
    case StorageError         = 'STORAGE_ERROR';
    case BadRequest           = 'BAD_REQUEST';
    case ServerError          = 'SERVER_ERROR';
}
