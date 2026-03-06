<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-3xl text-cyan-200">{{ $autor->nome }}</h2>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-[280px_minmax(0,1fr)]">
        <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-4">
            @if ($autor->foto)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($autor->foto) }}" alt="Foto" class="w-full rounded-lg border border-cyan-300/20 object-cover" />
            @else
                <div class="flex h-80 items-center justify-center rounded-lg border border-dashed border-cyan-300/30 text-slate-400">Sem foto</div>
            @endif
        </div>

        <div class="space-y-5">
            <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5">
                <h3 class="font-display text-xl text-cyan-200">Biografia</h3>
                <p class="mt-3 whitespace-pre-line text-slate-200">{{ $autor->bibliografia ?: 'Sem biografia dispon&iacute;vel.' }}</p>
            </div>

            <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5">
                <h3 class="font-display text-xl text-cyan-200">Livros</h3>
                <div class="mt-3 space-y-3">
                    @forelse ($autor->livros as $livro)
                        <a href="{{ route('livros.show', $livro) }}" class="block rounded-lg border border-cyan-300/20 bg-slate-950/60 p-3 hover:border-cyan-300/50">
                            <p class="font-semibold text-cyan-200">{{ $livro->nome }}</p>
                            <p class="text-sm text-slate-300">{{ $livro->editora?->nome }} &middot; ISBN {{ $livro->isbn }}</p>
                        </a>
                    @empty
                        <p class="text-slate-400">Sem livros associados.</p>
                    @endforelse
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('autores.index') }}" class="btn">Voltar</a>
                <a href="{{ route('autores.edit', $autor) }}" class="btn btn-primary">Editar</a>
            </div>
        </div>
    </div>
</x-app-layout>



