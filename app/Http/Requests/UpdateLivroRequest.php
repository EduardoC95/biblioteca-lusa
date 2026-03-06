<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLivroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'isbn' => ['required', 'string', 'max:255'],
            'nome' => ['required', 'string', 'max:255'],
            'editora_id' => ['required', 'exists:editoras,id'],
            'autores' => ['required', 'array', 'min:1'],
            'autores.*' => ['required', 'exists:autores,id'],
            'sinopse' => ['nullable', 'string'],
            'capa_imagem' => ['nullable', 'image', 'max:4096'],
            'preco' => ['required', 'numeric', 'min:0'],
        ];
    }
}
