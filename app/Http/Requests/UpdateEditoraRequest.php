<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEditoraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'notas' => ['nullable', 'string'],
            'logotipo' => ['nullable', 'image', 'max:4096'],
        ];
    }
}
