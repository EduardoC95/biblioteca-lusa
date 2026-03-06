<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-display text-3xl text-cyan-200">Autores</h2>
            <a href="{{ route('autores.create') }}" class="btn btn-primary">Novo Autor</a>
        </div>
    </x-slot>

    <form method="GET" class="grid gap-3 rounded-xl border border-cyan-300/20 bg-slate-900/70 p-4 md:grid-cols-5">
        <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Pesquisar..." class="input input-bordered md:col-span-2" />

        <select name="has_photo" class="select select-bordered">
            <option value="">Foto: todos</option>
            <option value="1" @selected($filters['has_photo'] === '1')>Com foto</option>
            <option value="0" @selected($filters['has_photo'] === '0')>Sem foto</option>
        </select>

        <select name="sort" class="select select-bordered">
            <option value="nome" @selected($filters['sort'] === 'nome')>Ordenar por nome</option>
            <option value="created_at" @selected($filters['sort'] === 'created_at')>Ordenar por data</option>
        </select>

        <select name="direction" class="select select-bordered">
            <option value="asc" @selected($filters['direction'] === 'asc')>Ascendente</option>
            <option value="desc" @selected($filters['direction'] === 'desc')>Descendente</option>
        </select>

        <div class="md:col-span-5 flex gap-2">
            <button class="btn btn-primary" type="submit">Aplicar</button>
            <a href="{{ route('autores.index') }}" class="btn btn-outline">Limpar</a>
        </div>
    </form>

    <div class="mt-6 overflow-x-auto rounded-xl border border-cyan-300/20 bg-slate-900/70">
        <table class="table futuristic-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Foto</th>
                    <th>Bibliografia</th>
                    <th class="text-right">Ac&ccedil;&otilde;es</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($autores as $autor)
                    <tr>
                        <td class="font-semibold"><a href="{{ route('autores.show', $autor) }}" class="text-cyan-200 hover:text-cyan-100 underline-offset-4 hover:underline">{{ $autor->nome }}</a></td>
                        <td>
                            @if ($autor->foto)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($autor->foto) }}" class="h-16 rounded" alt="Foto" />
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit((string) $autor->bibliografia, 120) }}</td>
                        <td>
                            <div class="flex justify-end gap-2">
                                <a class="btn btn-outline btn-sm" href="{{ route('autores.edit', $autor) }}">Editar</a>
                                <form method="POST" action="{{ route('autores.destroy', $autor) }}" onsubmit="return confirm('Remover este autor?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-error btn-sm" type="submit">Apagar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Sem registos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $autores->links() }}</div>
</x-app-layout>


