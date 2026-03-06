<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-3xl text-cyan-200">Painel da Biblioteca</h2>
    </x-slot>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('livros.index') }}" class="group block rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5 transition duration-200 hover:-translate-y-1 hover:shadow-lg">
            <div class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path d="M4 6.5A2.5 2.5 0 0 1 6.5 4H20v16H6.5A2.5 2.5 0 0 0 4 22V6.5Z"/><path d="M4 6.5A2.5 2.5 0 0 1 6.5 4H20"/></svg>
                Livros
            </div>
            <p class="text-4xl font-semibold">{{ $livrosCount }}</p>
        </a>

        <a href="{{ route('autores.index') }}" class="group block rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5 transition duration-200 hover:-translate-y-1 hover:shadow-lg">
            <div class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Autores
            </div>
            <p class="text-4xl font-semibold">{{ $autoresCount }}</p>
        </a>

        <a href="{{ route('editoras.index') }}" class="group block rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5 transition duration-200 hover:-translate-y-1 hover:shadow-lg">
            <div class="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5"><path d="M4 3h10a2 2 0 0 1 2 2v16H6a2 2 0 0 1-2-2V3z"/><path d="M16 8h4v13h-4"/><path d="M8 7h4"/><path d="M8 11h4"/></svg>
                Editoras
            </div>
            <p class="text-4xl font-semibold">{{ $editorasCount }}</p>
        </a>

        <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5 transition duration-200 hover:-translate-y-1 hover:shadow-lg">
            <div class="mb-2 text-sm font-semibold text-slate-500">Pre&ccedil;o m&eacute;dio</div>
            <p class="text-4xl font-semibold">{{ number_format((float) $precoMedio, 2, ',', '.') }} EUR</p>
        </div>
    </div>

    <div class="mt-5 grid gap-4 xl:grid-cols-3">
        <section class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5 transition duration-200 hover:-translate-y-1 hover:shadow-lg">
            <div class="mb-4 rounded-lg border border-cyan-300/20 bg-slate-900/50 p-3">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="font-display text-2xl text-cyan-200">Livros mais requisitados</h3>
                    <a href="{{ route('livros.index') }}" class="rounded-md border border-cyan-300/20 bg-slate-900/70 px-3 py-1 text-sm font-semibold transition hover:-translate-y-0.5 hover:shadow">Ver livros</a>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-cyan-300/15 bg-slate-900/35 p-2">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Editora</th>
                            <th class="text-right">Req.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($livrosMaisRequisitados as $livro)
                            <tr class="transition hover:bg-slate-800/20">
                                <td>
                                    <a href="{{ route('livros.show', $livro) }}" class="font-semibold underline-offset-4 hover:underline">{{ $livro->nome }}</a>
                                </td>
                                <td>{{ $livro->editora?->nome ?? '-' }}</td>
                                <td class="text-right">{{ $temRequisicoes ? (int) $livro->total_requisicoes : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Sem dados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5 transition duration-200 hover:-translate-y-1 hover:shadow-lg">
            <div class="mb-4 rounded-lg border border-cyan-300/20 bg-slate-900/50 p-3">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="font-display text-2xl text-cyan-200">Editora com mais livros</h3>
                    <a href="{{ route('editoras.index') }}" class="rounded-md border border-cyan-300/20 bg-slate-900/70 px-3 py-1 text-sm font-semibold transition hover:-translate-y-0.5 hover:shadow">Ver editoras</a>
                </div>
            </div>

            <div class="rounded-lg border border-cyan-300/15 bg-slate-900/35 p-4 transition hover:shadow">
                @if ($topEditora)
                    <p class="text-sm text-slate-500">Top editora</p>
                    <a href="{{ route('editoras.show', $topEditora) }}" class="mt-2 inline-block text-4xl font-display underline-offset-4 hover:underline">{{ $topEditora->nome }}</a>
                    <p class="mt-2 text-lg">{{ $topEditora->livros_count }} livro(s)</p>
                @else
                    <p>Sem editoras registadas.</p>
                @endif
            </div>
        </section>

        <section class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5 transition duration-200 hover:-translate-y-1 hover:shadow-lg">
            <div class="mb-4 rounded-lg border border-cyan-300/20 bg-slate-900/50 p-3">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="font-display text-2xl text-cyan-200">Livros por editora</h3>
                    <span class="rounded-md border border-cyan-300/20 bg-slate-900/70 px-3 py-1 text-sm font-semibold">Top 6</span>
                </div>
            </div>

            @php
                $maxLivros = max(1, (int) ($livrosPorEditora->max('livros_count') ?? 1));
            @endphp

            <div class="space-y-3 rounded-lg border border-cyan-300/15 bg-slate-900/35 p-3">
                @forelse ($livrosPorEditora as $editora)
                    <div class="rounded-md border border-transparent p-2 transition hover:border-cyan-300/20 hover:bg-slate-800/20">
                        <div class="mb-1 flex items-center justify-between gap-2 text-sm">
                            <a href="{{ route('editoras.show', $editora) }}" class="truncate underline-offset-4 hover:underline">{{ $editora->nome }}</a>
                            <span class="font-semibold">{{ $editora->livros_count }}</span>
                        </div>
                        <progress class="progress w-full" value="{{ $editora->livros_count }}" max="{{ $maxLivros }}"></progress>
                    </div>
                @empty
                    <p>Sem editoras registadas.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>


