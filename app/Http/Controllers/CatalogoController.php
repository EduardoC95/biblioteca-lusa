<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CatalogoController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));

        $livros = Livro::query()
            ->with(['editora', 'autores', 'requisicaoAtiva'])
            ->get()
            ->filter(function (Livro $livro) use ($search): bool {
                if ($search === '') {
                    return true;
                }

                return str_contains(mb_strtolower($livro->nome), mb_strtolower($search))
                    || str_contains(mb_strtolower($livro->isbn), mb_strtolower($search))
                    || str_contains(mb_strtolower((string) ($livro->editora?->nome ?? '')), mb_strtolower($search));
            })
            ->values();

        $livrosPaginator = $this->paginateCollection($livros, 10);

        return view('catalogo.index', [
            'livros' => $livrosPaginator,
            'search' => $search,
        ]);
    }

    public function show(Livro $livro): View
    {
        $livro->load(['editora', 'autores', 'requisicaoAtiva.cidadao']);

        return view('catalogo.show', [
            'livro' => $livro,
        ]);
    }

    private function paginateCollection(Collection $items, int $perPage): LengthAwarePaginator
    {
        $page = LengthAwarePaginator::resolveCurrentPage();
        $results = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $results,
            $items->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => request()->query(),
            ]
        );
    }
}
