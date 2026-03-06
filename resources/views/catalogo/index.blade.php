<x-guest-layout>
    <div class="mx-auto max-w-6xl px-4 py-8">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h1 class="font-display text-4xl text-cyan-200">Catálogo Público</h1>
            <div class="flex gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-outline">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Registar</a>
                @endauth
            </div>
        </div>

        <form method="GET" class="mb-6 rounded-xl border border-cyan-300/20 bg-slate-900/70 p-4">
            <input type="text" name="q" value="{{ $search }}" placeholder="Pesquisar por nome, ISBN ou editora" class="input input-bordered w-full" />
        </form>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($livros as $livro)
                <article class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-4">
                    <p class="text-xs uppercase tracking-widest {{ $livro->requisicaoAtiva ? 'text-amber-300' : 'text-emerald-300' }}">
                        {{ $livro->requisicaoAtiva ? 'Indisponível' : 'Disponível' }}
                    </p>
                    <h2 class="mt-2 text-xl font-semibold text-cyan-100">
                        <a href="{{ route('catalogo.show', $livro) }}" class="hover:underline">{{ $livro->nome }}</a>
                    </h2>
                    <p class="mt-1 text-sm text-slate-300">ISBN {{ $livro->isbn }}</p>
                    <p class="text-sm text-slate-300">{{ $livro->editora?->nome ?? '-' }}</p>

                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('catalogo.show', $livro) }}" class="btn btn-outline btn-sm">Detalhe</a>
                        @auth
                            <a href="{{ route('requisicoes.index', ['livro_id' => $livro->id]) }}" class="btn btn-primary btn-sm {{ $livro->requisicaoAtiva ? 'btn-disabled' : '' }}">
                                Requisitar
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Entrar para requisitar</a>
                        @endauth
                    </div>
                </article>
            @empty
                <p class="text-slate-400">Sem livros no catálogo.</p>
            @endforelse
        </div>
    </div>
</x-guest-layout>
