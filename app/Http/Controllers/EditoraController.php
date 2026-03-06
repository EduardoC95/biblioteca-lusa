<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEditoraRequest;
use App\Http\Requests\UpdateEditoraRequest;
use App\Models\Editora;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EditoraController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $sort = in_array($request->get('sort'), ['nome', 'livros_count', 'created_at'], true) ? $request->get('sort') : 'nome';
        $direction = $request->get('direction') === 'desc' ? 'desc' : 'asc';
        $hasLogo = $request->get('has_logo');

        $baseQuery = Editora::query();

        if ($hasLogo === '1') {
            $baseQuery->whereNotNull('logotipo');
        } elseif ($hasLogo === '0') {
            $baseQuery->whereNull('logotipo');
        }

        $canUseDbPagination = $search === '' && in_array($sort, ['livros_count', 'created_at'], true);

        if ($canUseDbPagination) {
            $editorasQuery = $sort === 'livros_count'
                ? $baseQuery->withCount('livros')->orderBy('livros_count', $direction)
                : $baseQuery->withCount('livros')->orderBy('created_at', $direction);

            $editorasPaginator = $editorasQuery->paginate(10)->withQueryString();
        } else {
            $editoras = $baseQuery->withCount('livros')->get()->filter(function (Editora $editora) use ($search): bool {
                if ($search === '') {
                    return true;
                }

                return str_contains(mb_strtolower($editora->nome), mb_strtolower($search))
                    || str_contains(mb_strtolower((string) $editora->notas), mb_strtolower($search));
            });

            $editoras = $this->sort($editoras, $sort, $direction);
            $editorasPaginator = $this->paginateCollection($editoras, 10);
        }

        return view('editoras.index', [
            'editoras' => $editorasPaginator,
            'filters' => [
                'q' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'has_logo' => $hasLogo,
            ],
        ]);
    }

    public function create(): View
    {
        return view('editoras.create');
    }

    public function store(StoreEditoraRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['logotipo'] = $request->hasFile('logotipo')
            ? $request->file('logotipo')->store('editoras', 'public')
            : null;

        Editora::create($data);

        return redirect()->route('editoras.index')->with('status', 'Editora criada com sucesso.');
    }

    public function show(Editora $editora): View
    {
        $editora->load(['livros.autores']);

        return view('editoras.show', [
            'editora' => $editora,
            'livrosCount' => $editora->livros->count(),
        ]);
    }

    public function edit(Editora $editora): View
    {
        return view('editoras.edit', compact('editora'));
    }

    public function update(UpdateEditoraRequest $request, Editora $editora): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('logotipo')) {
            if ($editora->logotipo) {
                Storage::disk('public')->delete($editora->logotipo);
            }

            $data['logotipo'] = $request->file('logotipo')->store('editoras', 'public');
        }

        $editora->update($data);

        return redirect()->route('editoras.index')->with('status', 'Editora atualizada com sucesso.');
    }

    public function destroy(Editora $editora): RedirectResponse
    {
        if ($editora->logotipo) {
            Storage::disk('public')->delete($editora->logotipo);
        }

        $editora->delete();

        return redirect()->route('editoras.index')->with('status', 'Editora removida com sucesso.');
    }

    private function sort(Collection $items, string $sort, string $direction): Collection
    {
        $sorted = $items->sortBy(function (Editora $editora) use ($sort) {
            return match ($sort) {
                'livros_count' => (int) $editora->livros_count,
                'created_at' => $editora->created_at?->timestamp ?? 0,
                default => mb_strtolower((string) $editora->nome),
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
