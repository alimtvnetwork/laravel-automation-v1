<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Student
 */
final class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'email'     => $this->email,
            'createdAt' => optional($this->created_at)->toIso8601String(),
            'updatedAt' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
