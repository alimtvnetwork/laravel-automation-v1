<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;

/**
 * DB isolation for Student — split-db ready per spec/05-split-db-architecture.
 */
final class StudentRepository
{
    public function all(): Collection
    {
        return Student::query()->orderBy('id')->get();
    }

    public function find(int $id): ?Student
    {
        return Student::query()->find($id);
    }

    public function create(array $attributes): Student
    {
        return Student::query()->create($attributes);
    }

    public function update(Student $student, array $attributes): Student
    {
        $student->fill($attributes)->save();
        return $student;
    }

    public function delete(Student $student): void
    {
        $student->delete();
    }
}
