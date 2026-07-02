<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentStoreRequest;
use App\Http\Requests\StudentUpdateRequest;
use App\Http\Resources\StudentResource;
use App\Http\Responses\Envelope;
use App\Services\StudentService;
use Illuminate\Http\JsonResponse;

final class StudentController extends Controller
{
    public function __construct(private readonly StudentService $service)
    {
    }

    public function index(): JsonResponse
    {
        $students = $this->service->list();
        return Envelope::success(StudentResource::collection($students)->resolve());
    }

    public function show(int $id): JsonResponse
    {
        $student = $this->service->get($id);
        return Envelope::success((new StudentResource($student))->resolve(request()));
    }

    public function store(StudentStoreRequest $request): JsonResponse
    {
        $student = $this->service->create($request->validated());
        return Envelope::success(
            (new StudentResource($student))->resolve($request),
            HttpStatus::Created,
            'Student created',
        );
    }

    public function update(int $id, StudentUpdateRequest $request): JsonResponse
    {
        $student = $this->service->update($id, $request->validated());
        return Envelope::success(
            (new StudentResource($student))->resolve($request),
            HttpStatus::Ok,
            'Student updated',
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);
        return Envelope::success(null, HttpStatus::Ok, 'Student deleted');
    }
}
