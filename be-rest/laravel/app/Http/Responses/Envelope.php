<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Constants\ApiConstants;
use App\Enums\ErrorCode;
use App\Enums\HttpStatus;
use Illuminate\Http\JsonResponse;

/**
 * Unified JSON envelope per spec/21-app/00-rest-general/spec.md §5.
 */
final class Envelope
{
    public static function success(mixed $data, HttpStatus $status = HttpStatus::Ok, string $message = 'OK'): JsonResponse
    {
        return new JsonResponse([
            'status'  => ApiConstants::STATUS_SUCCESS,
            'data'    => $data,
            'message' => $message,
            'error'   => null,
        ], $status->value);
    }

    public static function error(ErrorCode $code, string $message, HttpStatus $status, array $details = []): JsonResponse
    {
        return new JsonResponse([
            'status'  => ApiConstants::STATUS_ERROR,
            'data'    => null,
            'message' => $message,
            'error'   => [
                'code'    => $code->value,
                'details' => $details,
            ],
        ], $status->value);
    }
}
