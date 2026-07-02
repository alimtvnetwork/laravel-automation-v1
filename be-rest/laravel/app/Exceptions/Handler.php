<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\ErrorCode;
use App\Enums\HttpStatus;
use App\Http\Responses\Envelope;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Central exception → envelope mapping per spec §5.
 */
final class Handler
{
    public static function register(Exceptions $exceptions): void
    {
        $exceptions->render(fn (Throwable $e, Request $request) => self::toEnvelope($e));
    }

    private static function toEnvelope(Throwable $e): JsonResponse
    {
        if ($e instanceof ApiException) {
            return Envelope::error($e->code(), $e->getMessage(), $e->status());
        }

        if ($e instanceof ValidationException) {
            return Envelope::error(
                ErrorCode::ValidationError,
                'Validation failed',
                HttpStatus::UnprocessableEntity,
                $e->errors(),
            );
        }

        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            return Envelope::error(ErrorCode::NotFound, 'Resource not found', HttpStatus::NotFound);
        }

        if ($e instanceof HttpExceptionInterface) {
            $status = HttpStatus::tryFrom($e->getStatusCode()) ?? HttpStatus::ServerError;
            return Envelope::error(
                self::codeForStatus($status),
                $e->getMessage() !== '' ? $e->getMessage() : 'Request failed',
                $status,
            );
        }

        return Envelope::error(
            ErrorCode::ServerError,
            'Unhandled server error',
            HttpStatus::ServerError,
            ['exception' => $e->getMessage()],
        );
    }

    private static function codeForStatus(HttpStatus $status): ErrorCode
    {
        return match ($status) {
            HttpStatus::BadRequest          => ErrorCode::BadRequest,
            HttpStatus::Unauthorized        => ErrorCode::Unauthorized,
            HttpStatus::Forbidden           => ErrorCode::Forbidden,
            HttpStatus::NotFound            => ErrorCode::NotFound,
            HttpStatus::PayloadTooLarge     => ErrorCode::PayloadTooLarge,
            HttpStatus::UnsupportedMedia    => ErrorCode::UnsupportedMediaType,
            HttpStatus::UnprocessableEntity => ErrorCode::ValidationError,
            default                         => ErrorCode::ServerError,
        };
    }
}
