<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-display text-3xl text-cyan-200">Editoras</h2>
            <a href="{{ route('editoras.create') }}" class="btn btn-primary">Nova Editora</a>
        </div>
    </x-slot>

    <form method="GET" class="grid gap-3 rounded-xl border border-cyan-300/20 bg-slate-900/70 p-4 md:grid-cols-5">
        <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Pesquisar..." class="input input-bordered md:col-span-2" />

        <select name="has_logo" class="select select-bordered">
            <option value="">Logotipo: todos</option>
            <option value="1" @selected($filters['has_logo'] === '1')>Com logotipo</option>
            <option value="0" @selected($filters['has_logo'] === '0')>Sem logotipo</option>
        </select>

        <select name="sort" class="select select-bordered">
            <option value="nome" @selected($filters['sort'] === 'nome')>Ordenar por nome</option>
            <option value="livros_count" @selected($filters['sort'] === 'livros_count')>Ordenar por n&uacute;mero de livros</option>
            <option value="created_at" @selected($filters['sort'] === 'created_at')>Ordenar por data</option>
        </select>

        <select name="direction" class="select select-bordered">
            <option value="asc" @selected($filters['direction'] === 'asc')>Ascendente</option>
            <option value="desc" @selected($filters['direction'] === 'desc')>Descendente</option>
        </select>

        <div class="md:col-span-5 flex gap-2">
            <button class="btn btn-primary" type="submit">Aplicar</button>
            <a href="{{ route('editoras.index') }}" class="btn btn-outline">Limpar</a>
        </div>
    </form>

    <div class="mt-6 overflow-x-auto rounded-xl border border-cyan-300/20 bg-slate-900/70">
        <table class="table futuristic-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Logotipo</th>
                    <th>Livros</th>
                    <th>Notas</th>
                    <th class="text-right">Ac&ccedil;&otilde;es</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($editoras as $editora)
                    <tr>
                        <td class="font-semibold">
                            <a href="{{ route('editoras.show', $editora) }}" class="text-cyan-200 hover:text-cyan-100 underline-offset-4 hover:underline">{{ $editora->nome }}</a>
                        </td>
                        <td>
                            @if ($editora->logotipo)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($editora->logotipo) }}" class="h-16 rounded" alt="Logotipo" />
                            @else
                                -
                            @endif
                        </td>
                        <td><span class="badge badge-outline border-cyan-300/40 text-cyan-200">{{ $editora->livros_count }}</span></td>
                        <td>{{ \Illuminate\Support\Str::limit((string) $editora->notas, 120) }}</td>
                        <td>
                            <div class="flex justify-end gap-2">
                                <a class="btn btn-outline btn-sm" href="{{ route('editoras.edit', $editora) }}">Editar</a>
                                <form method="POST" action="{{ route('editoras.destroy', $editora) }}" onsubmit="return confirm('Remover esta editora?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-error btn-sm" type="submit">Apagar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Sem registos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $editoras->links() }}</div>
</x-app-layout>



