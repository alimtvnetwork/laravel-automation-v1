<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\FileUploadRequest;
use App\Http\Responses\Envelope;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class FileController extends Controller
{
    public function __construct(private readonly FileService $service)
    {
    }

    public function upload(FileUploadRequest $request): JsonResponse
    {
        $record = $this->service->upload(
            $request->file('file'),
            (string) $request->input('ownerId'),
        );

        return Envelope::success(
            [
                'fileId'   => $record->id,
                'filename' => $record->filename,
                'size'     => $record->size,
                'mime'     => $record->mime,
                'url'      => url('/file-serve/'.$record->id),
            ],
            HttpStatus::Created,
            'File uploaded',
        );
    }

    public function serve(int $id): StreamedResponse
    {
        $record = $this->service->get($id);
        $stream = $this->service->readStream($record);

        return response()->stream(
            function () use ($stream): void {
                fpassthru($stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }
            },
            HttpStatus::Ok->value,
            [
                'Content-Type'        => $record->mime,
                'Content-Length'      => (string) $record->size,
                'Content-Disposition' => 'inline; filename="'.addslashes($record->filename).'"',
            ],
        );
    }
}
