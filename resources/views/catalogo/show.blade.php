<x-guest-layout>
    <div class="mx-auto max-w-5xl px-4 py-8">
        <a href="{{ route('catalogo.index') }}" class="btn btn-outline btn-sm">Voltar ao catálogo</a>

        <div class="mt-4 grid gap-6 rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5 lg:grid-cols-[260px_minmax(0,1fr)]">
            <div>
                @if ($livro->capa_imagem)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($livro->capa_imagem) }}" alt="Capa" class="w-full rounded-lg" />
                @else
                    <div class="flex h-80 items-center justify-center rounded-lg border border-dashed border-cyan-300/30 text-slate-400">Sem capa</div>
                @endif
            </div>

            <div>
                <p class="text-xs uppercase tracking-widest {{ $livro->requisicaoAtiva ? 'text-amber-300' : 'text-emerald-300' }}">
                    {{ $livro->requisicaoAtiva ? 'Indisponível para requisição' : 'Disponível para requisição' }}
                </p>
                <h1 class="mt-2 font-display text-4xl text-cyan-100">{{ $livro->nome }}</h1>
                <p class="mt-2 text-slate-200">ISBN {{ $livro->isbn }}</p>
                <p class="text-slate-200">Editora: {{ $livro->editora?->nome ?? '-' }}</p>
                <p class="mt-3 whitespace-pre-line text-slate-300">{{ $livro->sinopse ?: 'Sem sinopse disponível.' }}</p>

                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ($livro->autores as $autor)
                        <span class="badge badge-lg border-cyan-300/40 bg-slate-950 text-cyan-200">{{ $autor->nome }}</span>
                    @endforeach
                </div>

                <div class="mt-6">
                    @auth
                        <a href="{{ route('requisicoes.index', ['livro_id' => $livro->id]) }}" class="btn btn-primary {{ $livro->requisicaoAtiva ? 'btn-disabled' : '' }}">Requisitar</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">Entrar para requisitar</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
