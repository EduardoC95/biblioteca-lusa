<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAutorRequest;
use App\Http\Requests\UpdateAutorRequest;
use App\Models\Autor;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AutorController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $sort = in_array($request->get('sort'), ['nome', 'created_at'], true) ? $request->get('sort') : 'nome';
        $direction = $request->get('direction') === 'desc' ? 'desc' : 'asc';
        $hasPhoto = $request->get('has_photo');

        $baseQuery = Autor::query();

        if ($hasPhoto === '1') {
            $baseQuery->whereNotNull('foto');
        } elseif ($hasPhoto === '0') {
            $baseQuery->whereNull('foto');
        }

        if ($search === '' && $sort === 'created_at') {
            $autoresPaginator = $baseQuery
                ->orderBy('created_at', $direction)
                ->paginate(10)
                ->withQueryString();
        } else {
            $autores = $baseQuery->get()->filter(function (Autor $autor) use ($search): bool {
                if ($search === '') {
                    return true;
                }

                return str_contains(mb_strtolower($autor->nome), mb_strtolower($search))
                    || str_contains(mb_strtolower((string) $autor->bibliografia), mb_strtolower($search));
            });

            $autores = $this->sort($autores, $sort, $direction);
            $autoresPaginator = $this->paginateCollection($autores, 10);
        }

        return view('autores.index', [
            'autores' => $autoresPaginator,
            'filters' => [
                'q' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'has_photo' => $hasPhoto,
            ],
        ]);
    }

    public function create(): View
    {
        return view('autores.create');
    }

    public function store(StoreAutorRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['foto'] = $request->hasFile('foto')
            ? $request->file('foto')->store('autores', 'public')
            : null;

        $autor = Autor::create($data);

        ActivityLogger::log(
            userId: $request->user()?->id,
            module: 'autores',
            objectId: $autor->id,
            action: 'create',
            description: 'Autor criado: ' . $autor->nome,
            request: $request
        );

        return redirect()->route('autores.index')->with('status', 'Autor criado com sucesso.');
    }

    public function show(Autor $autor): View
    {
        $autor->load(['livros.editora']);

        return view('autores.show', compact('autor'));
    }

    public function edit(Autor $autor): View
    {
        return view('autores.edit', compact('autor'));
    }

    public function update(UpdateAutorRequest $request, Autor $autor): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            if ($autor->foto) {
                Storage::disk('public')->delete($autor->foto);
            }

            $data['foto'] = $request->file('foto')->store('autores', 'public');
        }

        $autor->update($data);

        ActivityLogger::log(
            userId: $request->user()?->id,
            module: 'autores',
            objectId: $autor->id,
            action: 'update',
            description: 'Autor atualizado: ' . $autor->nome,
            request: $request
        );

        return redirect()->route('autores.index')->with('status', 'Autor atualizado com sucesso.');
    }

    public function destroy(Request $request, Autor $autor): RedirectResponse
    {
        $autorNome = $autor->nome;
        $autorId = $autor->id;

        if ($autor->foto) {
            Storage::disk('public')->delete($autor->foto);
        }

        $autor->delete();

        ActivityLogger::log(
            userId: $request->user()?->id,
            module: 'autores',
            objectId: $autorId,
            action: 'delete',
            description: 'Autor removido: ' . $autorNome,
            request: $request
        );

        return redirect()->route('autores.index')->with('status', 'Autor removido com sucesso.');
    }

    private function sort(Collection $items, string $sort, string $direction): Collection
    {
        $sorted = $items->sortBy(function (Autor $autor) use ($sort) {
            if ($sort === 'created_at') {
                return $autor->created_at?->timestamp ?? 0;
            }

            return mb_strtolower((string) $autor->nome);
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
