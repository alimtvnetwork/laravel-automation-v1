<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class FileUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_accepts_allowed_mime(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->image('photo.png', 8, 8);

        $response = $this->postJson('/file-upload', [
            'ownerId' => 'user-1',
            'file'    => $file,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.mime', 'image/png');
    }

    public function test_upload_rejects_unsupported_mime(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('note.txt', 1, 'text/plain');

        $this->postJson('/file-upload', ['ownerId' => 'u', 'file' => $file])
            ->assertStatus(415)
            ->assertJsonPath('error.code', 'UNSUPPORTED_MEDIA_TYPE');
    }

    public function test_upload_rejects_oversize(): void
    {
        Storage::fake('local');
        // 11 MB PDF
        $file = UploadedFile::fake()->create('big.pdf', 11 * 1024, 'application/pdf');

        $this->postJson('/file-upload', ['ownerId' => 'u', 'file' => $file])
            ->assertStatus(413)
            ->assertJsonPath('error.code', 'PAYLOAD_TOO_LARGE');
    }

    public function test_upload_missing_fields_returns_422(): void
    {
        $this->postJson('/file-upload', [])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
    }
}
