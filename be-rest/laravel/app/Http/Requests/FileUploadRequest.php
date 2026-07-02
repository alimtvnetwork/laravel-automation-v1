<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates ownerId + file presence per spec §4.2.
 * MIME/size are validated in FileService to emit spec-correct 415/413 codes.
 */
final class FileUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ownerId' => ['required', 'string', 'max:120'],
            'file'    => ['required', 'file'],
        ];
    }
}
