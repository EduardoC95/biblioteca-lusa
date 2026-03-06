<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-3xl text-cyan-200">{{ $editora->nome }}</h2>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-[280px_minmax(0,1fr)]">
        <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-4">
            @if ($editora->logotipo)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($editora->logotipo) }}" alt="Logotipo" class="w-full rounded-lg border border-cyan-300/20 object-cover" />
            @else
                <div class="flex h-64 items-center justify-center rounded-lg border border-dashed border-cyan-300/30 text-slate-400">Sem logotipo</div>
            @endif

            <div class="mt-4 rounded-lg border border-cyan-300/20 bg-slate-950/70 p-4">
                <p class="text-xs uppercase tracking-widest text-cyan-300">N&uacute;mero de livros</p>
                <p class="mt-2 text-4xl font-display text-cyan-200">{{ $livrosCount }}</p>
            </div>
        </div>

        <div class="space-y-5">
            <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5">
                <h3 class="font-display text-xl text-cyan-200">Notas</h3>
                <p class="mt-3 whitespace-pre-line text-slate-200">{{ $editora->notas ?: 'Sem notas dispon&iacute;veis.' }}</p>
            </div>

            <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5">
                <h3 class="font-display text-xl text-cyan-200">Livros desta editora</h3>
                <div class="mt-3 space-y-3">
                    @forelse ($editora->livros as $livro)
                        <a href="{{ route('livros.show', $livro) }}" class="block rounded-lg border border-cyan-300/20 bg-slate-950/60 p-3 hover:border-cyan-300/50">
                            <p class="font-semibold text-cyan-200">{{ $livro->nome }}</p>
                            <p class="text-sm text-slate-300">ISBN {{ $livro->isbn }} &middot; {{ number_format((float) $livro->preco, 2, ',', '.') }} EUR</p>
                        </a>
                    @empty
                        <p class="text-slate-400">Sem livros associados.</p>
                    @endforelse
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('editoras.index') }}" class="btn btn-outline">Voltar</a>
                <a href="{{ route('editoras.edit', $editora) }}" class="btn btn-primary">Editar</a>
            </div>
        </div>
    </div>
</x-app-layout>


