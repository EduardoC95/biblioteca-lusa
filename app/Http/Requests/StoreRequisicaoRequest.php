<?php

namespace App\Http\Requests;

use App\Models\Livro;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreRequisicaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $rules = [
            'livro_id' => ['required', 'exists:livros,id'],
        ];

        if ($this->user()?->isAdmin()) {
            $rules['cidadao_id'] = ['required', 'exists:users,id'];
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $livroId = (int) $this->input('livro_id');
            $cidadaoId = $this->user()->isAdmin()
                ? (int) $this->input('cidadao_id')
                : (int) $this->user()->id;

            $livro = Livro::query()->find($livroId);
            if (! $livro) {
                return;
            }

            if (! $livro->estaDisponivelParaRequisicao()) {
                $validator->errors()->add('livro_id', 'Este livro já está numa requisição ativa.');
            }

            $cidadao = User::query()->find($cidadaoId);
            if (! $cidadao || ! $cidadao->isCidadao()) {
                $validator->errors()->add('cidadao_id', 'A requisição deve ser associada a um cidadão.');

                return;
            }

            $requisicoesAtivas = $cidadao->requisicoes()->whereNull('data_real_entrega')->count();
            if ($requisicoesAtivas >= 3) {
                $validator->errors()->add('cidadao_id', 'Cada cidadão só pode ter 3 livros requisitados em simultâneo.');
            }
        });
    }

    public function cidadaoId(): int
    {
        return $this->user()->isAdmin()
            ? (int) $this->input('cidadao_id')
            : (int) $this->user()->id;
    }
}
