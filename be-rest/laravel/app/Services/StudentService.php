<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Student;
use App\Repositories\StudentRepository;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class StudentService
{
    public function __construct(private readonly StudentRepository $repository)
    {
    }

    public function list(): Collection
    {
        return $this->repository->all();
    }

    public function get(int $id): Student
    {
        $student = $this->repository->find($id);
        if ($student === null) {
            throw new NotFoundHttpException('Student not found');
        }
        return $student;
    }

    public function create(array $attributes): Student
    {
        return $this->repository->create($attributes);
    }

    public function update(int $id, array $attributes): Student
    {
        $student = $this->get($id);
        return $this->repository->update($student, $attributes);
    }

    public function delete(int $id): void
    {
        $student = $this->get($id);
        $this->repository->delete($student);
    }
}
