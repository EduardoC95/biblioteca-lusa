<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-display text-3xl text-cyan-200">Livros</h2>
            @if (auth()->user()->isAdmin())
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('livros.export') }}" class="btn btn-secondary">Exportar Excel</a>
                    <a href="{{ route('livros.create') }}" class="btn btn-primary">Novo Livro</a>
                </div>
            @endif
        </div>
    </x-slot>

    <form method="GET" class="grid gap-3 rounded-xl border border-cyan-300/20 bg-slate-900/70 p-4 md:grid-cols-6">
        <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Pesquisar..." class="input input-bordered md:col-span-2" />

        <select name="editora_id" class="select select-bordered">
            <option value="">Todas editoras</option>
            @foreach ($editoras as $editora)
                <option value="{{ $editora->id }}" @selected((int) $filters['editora_id'] === $editora->id)>{{ $editora->nome }}</option>
            @endforeach
        </select>

        <select name="autor_id" class="select select-bordered">
            <option value="">Todos autores</option>
            @foreach ($autores as $autor)
                <option value="{{ $autor->id }}" @selected((int) $filters['autor_id'] === $autor->id)>{{ $autor->nome }}</option>
            @endforeach
        </select>

        <input type="number" step="0.01" min="0" name="preco_min" value="{{ $filters['preco_min'] }}" placeholder="Preço min" class="input input-bordered" />
        <input type="number" step="0.01" min="0" name="preco_max" value="{{ $filters['preco_max'] }}" placeholder="Preço max" class="input input-bordered" />

        <div class="md:col-span-6 flex gap-2">
            <button class="btn btn-primary" type="submit">Aplicar</button>
            <a href="{{ route('livros.index') }}" class="btn btn-outline">Limpar</a>
        </div>
    </form>

    @php
        $currentSort = $filters['sort'] ?? 'nome';
        $currentDirection = $filters['direction'] ?? 'asc';

        $sortUrl = function (string $field) use ($currentSort, $currentDirection) {
            $nextDirection = ($currentSort === $field && $currentDirection === 'asc') ? 'desc' : 'asc';
            $query = array_merge(request()->query(), ['sort' => $field, 'direction' => $nextDirection]);
            unset($query['page']);

            return route('livros.index', $query);
        };

        $sortArrow = function (string $field) use ($currentSort, $currentDirection) {
            if ($currentSort !== $field) {
                return '&harr;';
            }

            return $currentDirection === 'asc' ? '&uarr;' : '&darr;';
        };
    @endphp

    <div class="mt-6 overflow-x-auto rounded-xl border border-cyan-300/20 bg-slate-900/70">
        <table class="table futuristic-table">
            <thead>
                <tr>
                    <th>ISBN</th>
                    <th>Capa</th>
                    <th>
                        <a href="{{ $sortUrl('nome') }}" class="inline-flex items-center gap-1 hover:underline">
                            <span>{!! $sortArrow('nome') !!}</span><span>Nome</span>
                        </a>
                    </th>
                    <th>Estado</th>
                    <th>
                        <a href="{{ $sortUrl('editora') }}" class="inline-flex items-center gap-1 hover:underline">
                            <span>{!! $sortArrow('editora') !!}</span><span>Editora</span>
                        </a>
                    </th>
                    <th>
                        <a href="{{ $sortUrl('autores') }}" class="inline-flex items-center gap-1 hover:underline">
                            <span>{!! $sortArrow('autores') !!}</span><span>Autores</span>
                        </a>
                    </th>
                    <th>
                        <a href="{{ $sortUrl('preco') }}" class="inline-flex items-center gap-1 hover:underline">
                            <span>{!! $sortArrow('preco') !!}</span><span>Preço</span>
                        </a>
                    </th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($livros as $livro)
                    <tr>
                        <td>{{ $livro->isbn }}</td>
                        <td>
                            @if ($livro->capa_imagem)
                                <img
                                    src="{{ \Illuminate\Support\Facades\Storage::url($livro->capa_imagem) }}"
                                    alt="Capa de {{ $livro->nome }}"
                                    class="h-14 w-10 rounded object-cover shadow-sm"
                                />
                            @else
                                <div class="flex h-14 w-10 items-center justify-center rounded border border-dashed border-slate-400/50 text-[10px] text-slate-500">
                                    sem capa
                                </div>
                            @endif
                        </td>
                        <td class="font-semibold"><a href="{{ route('livros.show', $livro) }}" class="text-cyan-200 hover:text-cyan-100 underline-offset-4 hover:underline">{{ $livro->nome }}</a></td>
                        <td>
                            <span class="badge {{ $livro->requisicaoAtiva ? 'badge-warning' : 'badge-success' }}">
                                {{ $livro->requisicaoAtiva ? 'Indisponível' : 'Disponível' }}
                            </span>
                        </td>
                        <td>
                            @if ($livro->editora)
                                <a href="{{ route('editoras.show', $livro->editora) }}" class="text-cyan-200 hover:text-cyan-100 underline-offset-4 hover:underline">
                                    {{ $livro->editora->nome }}
                                </a>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($livro->autores as $autor)
                                    <a href="{{ route('autores.show', $autor) }}" class="text-cyan-200 hover:text-cyan-100 underline-offset-4 hover:underline">{{ $autor->nome }}</a>@if(!$loop->last),@endif
                                @endforeach
                            </div>
                        </td>
                        <td>{{ number_format((float) $livro->preco, 2, ',', '.') }} EUR</td>
                        <td>
                            <div class="flex justify-end gap-2">
                                <a class="btn btn-outline btn-sm {{ $livro->requisicaoAtiva ? 'btn-disabled' : '' }}" href="{{ route('requisicoes.index', ['livro_id' => $livro->id]) }}">Requisitar</a>
                                @if (auth()->user()->isAdmin())
                                    <a class="btn btn-outline btn-sm" href="{{ route('livros.edit', $livro) }}">Editar</a>
                                    <form method="POST" action="{{ route('livros.destroy', $livro) }}" onsubmit="return confirm('Remover este livro?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-error btn-sm" type="submit">Apagar</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Sem registos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $livros->links() }}</div>
</x-app-layout>

