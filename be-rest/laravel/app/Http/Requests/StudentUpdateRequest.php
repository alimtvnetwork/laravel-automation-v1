<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StudentUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id       = (int) $this->route('id');
        $required = $this->isMethod('PUT') ? 'required' : 'sometimes';

        return [
            'name'  => [$required, 'string', 'min:1', 'max:120'],
            'email' => [$required, 'email', 'max:255', Rule::unique('students', 'email')->ignore($id)],
        ];
    }
}
