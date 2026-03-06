<?php

namespace App\Http\Controllers;

use App\Exports\LivrosExport;
use App\Http\Requests\StoreLivroRequest;
use App\Http\Requests\UpdateLivroRequest;
use App\Models\Autor;
use App\Models\Editora;
use App\Models\Livro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LivroController extends Controller
{

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $sort = in_array($request->get('sort'), ['nome', 'isbn', 'preco', 'editora', 'autores', 'created_at'], true)
            ? $request->get('sort')
            : 'nome';
        $direction = $request->get('direction') === 'desc' ? 'desc' : 'asc';
        $editoraId = $request->integer('editora_id') ?: null;
        $autorId = $request->integer('autor_id') ?: null;
        $precoMin = $request->get('preco_min');
        $precoMax = $request->get('preco_max');

        $baseQuery = Livro::query()->with(['editora', 'autores', 'requisicaoAtiva']);

        if ($editoraId) {
            $baseQuery->where('editora_id', $editoraId);
        }

        if ($autorId) {
            $baseQuery->whereHas('autores', fn ($q) => $q->where('autores.id', $autorId));
        }

        $canUseDbPagination = $search === ''
            && ($precoMin === null || $precoMin === '')
            && ($precoMax === null || $precoMax === '')
            && $sort === 'created_at';

        if ($canUseDbPagination) {
            $livrosPaginator = $baseQuery
                ->orderBy('created_at', $direction)
                ->paginate(10)
                ->withQueryString();
        } else {
            $livros = $baseQuery->get()->filter(function (Livro $livro) use ($search, $precoMin, $precoMax): bool {
                $preco = (float) $livro->preco;

                if ($precoMin !== null && $precoMin !== '' && $preco < (float) $precoMin) {
                    return false;
                }

                if ($precoMax !== null && $precoMax !== '' && $preco > (float) $precoMax) {
                    return false;
                }

                if ($search === '') {
                    return true;
                }

                $authorNames = $livro->autores->pluck('nome')->implode(' ');
                $editoraName = $livro->editora?->nome ?? '';

                return str_contains(mb_strtolower($livro->isbn), mb_strtolower($search))
                    || str_contains(mb_strtolower($livro->nome), mb_strtolower($search))
                    || str_contains(mb_strtolower((string) $livro->sinopse), mb_strtolower($search))
                    || str_contains(mb_strtolower($authorNames), mb_strtolower($search))
                    || str_contains(mb_strtolower($editoraName), mb_strtolower($search));
            });

            $livros = $this->sort($livros, $sort, $direction);
            $livrosPaginator = $this->paginateCollection($livros, 10);
        }

        return view('livros.index', [
            'livros' => $livrosPaginator,
            'editoras' => Editora::query()->get()->sortBy(fn (Editora $editora) => mb_strtolower($editora->nome))->values(),
            'autores' => Autor::query()->get()->sortBy(fn (Autor $autor) => mb_strtolower($autor->nome))->values(),
            'filters' => [
                'q' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'editora_id' => $editoraId,
                'autor_id' => $autorId,
                'preco_min' => $precoMin,
                'preco_max' => $precoMax,
            ],
        ]);
    }

    public function create(): View
    {
        return view('livros.create', [
            'editoras' => Editora::query()->get()->sortBy(fn (Editora $editora) => mb_strtolower($editora->nome))->values(),
            'autores' => Autor::query()->get()->sortBy(fn (Autor $autor) => mb_strtolower($autor->nome))->values(),
        ]);
    }

    public function store(StoreLivroRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $authorIds = $data['autores'];
        unset($data['autores']);

        $data['preco'] = number_format((float) $data['preco'], 2, '.', '');
        $data['capa_imagem'] = $request->hasFile('capa_imagem')
            ? $request->file('capa_imagem')->store('livros', 'public')
            : null;

        $livro = Livro::create($data);
        $livro->autores()->sync($authorIds);

        return redirect()->route('livros.index')->with('status', 'Livro criado com sucesso.');
    }

    public function show(Livro $livro): View
    {
        $livro->load([
            'editora',
            'autores',
            'requisicoes' => fn ($q) => $q->with('cidadao')->orderByDesc('created_at'),
        ]);

        return view('livros.show', compact('livro'));
    }

    public function edit(Livro $livro): View
    {
        $livro->load('autores');

        return view('livros.edit', [
            'livro' => $livro,
            'editoras' => Editora::query()->get()->sortBy(fn (Editora $editora) => mb_strtolower($editora->nome))->values(),
            'autores' => Autor::query()->get()->sortBy(fn (Autor $autor) => mb_strtolower($autor->nome))->values(),
        ]);
    }

    public function update(UpdateLivroRequest $request, Livro $livro): RedirectResponse
    {
        $data = $request->validated();
        $authorIds = $data['autores'];
        unset($data['autores']);

        $data['preco'] = number_format((float) $data['preco'], 2, '.', '');

        if ($request->hasFile('capa_imagem')) {
            if ($livro->capa_imagem) {
                Storage::disk('public')->delete($livro->capa_imagem);
            }

            $data['capa_imagem'] = $request->file('capa_imagem')->store('livros', 'public');
        }

        $livro->update($data);
        $livro->autores()->sync($authorIds);

        return redirect()->route('livros.index')->with('status', 'Livro atualizado com sucesso.');
    }

    public function destroy(Livro $livro): RedirectResponse
    {
        if ($livro->capa_imagem) {
            Storage::disk('public')->delete($livro->capa_imagem);
        }

        $livro->autores()->detach();
        $livro->delete();

        return redirect()->route('livros.index')->with('status', 'Livro removido com sucesso.');
    }

    public function export(): BinaryFileResponse
    {
        return Excel::download(new LivrosExport(), 'livros.xlsx');
    }

    private function sort(Collection $items, string $sort, string $direction): Collection
    {
        $sorted = $items->sortBy(function (Livro $livro) use ($sort) {
            return match ($sort) {
                'isbn' => mb_strtolower((string) $livro->isbn),
                'preco' => (float) $livro->preco,
                'editora' => mb_strtolower((string) ($livro->editora?->nome ?? '')),
                'autores' => mb_strtolower((string) $livro->autores->pluck('nome')->sort()->implode(', ')),
                'created_at' => $livro->created_at?->timestamp ?? 0,
                default => mb_strtolower((string) $livro->nome),
            };
        });

        return $direction === 'desc' ? $sorted->reverse()->values() : $sorted->values();
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

