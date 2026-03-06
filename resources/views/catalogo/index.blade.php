<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-3xl text-cyan-200">Cat&aacute;logo</h2>
    </x-slot>

    <form method="GET" class="mb-6 rounded-xl border border-cyan-300/20 bg-slate-900/70 p-4">
        <input type="text" name="q" value="{{ $search }}" placeholder="Pesquisar por nome, ISBN ou editora" class="input input-bordered w-full" />
    </form>

    <div class="grid gap-4 md:grid-cols-2">
        @forelse ($livros as $livro)
            <article class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-4">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs uppercase tracking-widest text-slate-400">
                            {{ $livro->requisicaoAtiva ? 'Indisponível' : 'Disponível' }}
                        </p>
                        <h2 class="mt-2 text-xl font-semibold text-cyan-100">
                            <a href="{{ route('catalogo.show', $livro) }}" class="hover:underline">{{ $livro->nome }}</a>
                        </h2>
                        <p class="mt-1 text-sm text-slate-300">ISBN {{ $livro->isbn }}</p>
                        <p class="text-sm text-slate-300">{{ $livro->editora?->nome ?? '-' }}</p>

                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('catalogo.show', $livro) }}" class="btn btn-outline btn-sm">Detalhe</a>
                            <a href="{{ route('requisicoes.index', ['livro_id' => $livro->id]) }}" class="btn btn-primary btn-sm {{ $livro->requisicaoAtiva ? 'btn-disabled' : '' }}">
                                Requisitar
                            </a>
                        </div>
                    </div>

                    <div class="w-20 shrink-0">
                        @if ($livro->capa_imagem)
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::url($livro->capa_imagem) }}"
                                alt="Capa de {{ $livro->nome }}"
                                class="h-28 w-20 rounded object-cover border border-cyan-300/20"
                            />
                        @else
                            <div class="flex h-28 w-20 items-center justify-center rounded border border-dashed border-cyan-300/30 text-[10px] text-slate-500">
                                sem capa
                            </div>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <p class="text-slate-400">Sem livros no cat&aacute;logo.</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $livros->links() }}</div>
</x-app-layout>
