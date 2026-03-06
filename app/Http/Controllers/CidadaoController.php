<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CidadaoController extends Controller
{
    public function index(Request $request): View
    {
        $cidadaos = User::query()
            ->where('role', User::ROLE_CIDADAO)
            ->withCount([
                'requisicoes as requisicoes_ativas_count' => fn ($q) => $q->whereNull('data_real_entrega'),
            ])
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('cidadaos.index', [
            'cidadaos' => $cidadaos,
        ]);
    }

    public function show(User $cidadao): View
    {
        abort_if(! $cidadao->isCidadao(), 404);

        $cidadao->load([
            'requisicoes' => fn ($q) => $q->with('livro.editora')->orderByDesc('created_at'),
        ]);

        return view('cidadaos.show', [
            'cidadao' => $cidadao,
        ]);
    }
}
