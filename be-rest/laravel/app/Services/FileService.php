<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ErrorCode;
use App\Enums\HttpStatus;
use App\Exceptions\ApiException;
use App\Models\FileRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * File upload/serve logic per spec §4.2 / §4.3.
 */
final class FileService
{
    private const ALLOWED_MIME = [
        'image/png',
        'image/jpeg',
        'image/webp',
        'application/pdf',
    ];

    private const MAX_BYTES = 10 * 1024 * 1024;

    public function upload(UploadedFile $file, string $ownerId): FileRecord
    {
        $this->assertMime($file);
        $this->assertSize($file);

        try {
            $path = $file->store('uploads', $this->disk());
        } catch (Throwable $e) {
            throw new StorageException('Failed to persist file: '.$e->getMessage());
        }

        return FileRecord::query()->create([
            'owner_id' => $ownerId,
            'filename' => $file->getClientOriginalName(),
            'mime'     => $file->getMimeType(),
            'size'     => $file->getSize(),
            'path'     => $path,
        ]);
    }

    public function get(int $id): FileRecord
    {
        $record = FileRecord::query()->find($id);
        if ($record === null) {
            throw new NotFoundHttpException('File not found');
        }
        return $record;
    }

    public function readStream(FileRecord $record)
    {
        if (Storage::disk($this->disk())->exists($record->path) === false) {
            throw new StorageException('File missing from storage');
        }
        return Storage::disk($this->disk())->readStream($record->path);
    }

    public function disk(): string
    {
        return (string) config('filesystems.default', 'local');
    }

    private function assertMime(UploadedFile $file): void
    {
        if (in_array($file->getMimeType(), self::ALLOWED_MIME, true)) {
            return;
        }
        throw new UnsupportedMediaTypeException('MIME type not allowed: '.$file->getMimeType());
    }

    private function assertSize(UploadedFile $file): void
    {
        if ($file->getSize() <= self::MAX_BYTES) {
            return;
        }
        throw new PayloadTooLargeException('File exceeds 10 MB limit');
    }
}

final class UnsupportedMediaTypeException extends ApiException
{
    public function errorCode(): int { return 415; }
    public function httpStatus(): int { return HttpStatus::UnprocessableEntity->value; }
}

final class PayloadTooLargeException extends ApiException
{
    public function errorCode(): int { return 413; }
    public function httpStatus(): int { return 413; }
}

final class StorageException extends ApiException
{
    public function errorCode(): int { return 500; }
    public function httpStatus(): int { return 500; }
}
