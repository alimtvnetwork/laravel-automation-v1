<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class StudentCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_returns_envelope(): void
    {
        Student::query()->create(['name' => 'Ada', 'email' => 'ada@example.com']);

        $this->getJson('/students')
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.0.email', 'ada@example.com');
    }

    public function test_create_returns_201(): void
    {
        $this->postJson('/students', ['name' => 'Grace', 'email' => 'grace@example.com'])
            ->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.name', 'Grace');
    }

    public function test_create_validation_error_returns_422(): void
    {
        $this->postJson('/students', ['name' => '', 'email' => 'nope'])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
    }

    public function test_show_missing_returns_404(): void
    {
        $this->getJson('/students/999')
            ->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND');
    }

    public function test_update_and_delete(): void
    {
        $student = Student::query()->create(['name' => 'Ada', 'email' => 'ada@example.com']);

        $this->putJson("/students/{$student->id}", ['name' => 'Ada L.', 'email' => 'ada@example.com'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Ada L.');

        $this->deleteJson("/students/{$student->id}")->assertOk();
        $this->getJson("/students/{$student->id}")->assertStatus(404);
    }
}
