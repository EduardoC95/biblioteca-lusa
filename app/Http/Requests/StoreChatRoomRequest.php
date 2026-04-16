<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChatRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'reference' => [
                'nullable',
                'string',
                'max:255',
                'unique:chat_rooms,reference'
            ],

            'avatar' => ['nullable', 'string', 'max:255'],

            'users' => ['nullable', 'array'],

            'users.*' => [
                'integer',
                'exists:users,id'
            ],
        ];
    }
}
