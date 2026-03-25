<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-3xl text-cyan-200">Detalhe da Review</h2>
    </x-slot>

    <div class="space-y-6">

        {{-- INFO --}}
        <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-6 space-y-4">

            <div>
                <p class="text-xs uppercase tracking-widest text-cyan-300">Livro</p>
                <p class="text-slate-100">{{ $review->livro?->nome }}</p>
            </div>

            <div>
                <p class="text-xs uppercase tracking-widest text-cyan-300">Cidadão</p>
                <p class="text-slate-100">
                    {{ $review->user?->name }} ({{ $review->user?->email }})
                </p>
            </div>

            <div>
                <p class="text-xs uppercase tracking-widest text-cyan-300">Estado</p>
                <p>
                    @if($review->estado === 'suspenso')
                        <span class="badge badge-warning">Suspenso</span>
                    @elseif($review->estado === 'ativo')
                        <span class="badge badge-success">Ativo</span>
                    @else
                        <span class="badge badge-error">Recusado</span>
                    @endif
                </p>
            </div>

            <div>
                <p class="text-xs uppercase tracking-widest text-cyan-300">Rating</p>
                <p class="text-slate-100">
                    {{ $review->rating ? $review->rating . '/5' : '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs uppercase tracking-widest text-cyan-300">Comentário</p>
                <p class="mt-2 text-slate-200 whitespace-pre-line">
                    {{ $review->comentario }}
                </p>
            </div>

            @if($review->justificacao_recusa)
                <div>
                    <p class="text-xs uppercase tracking-widest text-cyan-300">Justificação da recusa</p>
                    <p class="mt-2 text-red-300 whitespace-pre-line">
                        {{ $review->justificacao_recusa }}
                    </p>
                </div>
            @endif

        </div>

        {{-- MODERAÇÃO --}}
        @if($review->estado === 'suspenso')
            <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-6">

                <h3 class="font-display text-xl text-cyan-200 mb-4">Moderação</h3>

                <form method="POST" action="{{ route('admin.reviews.update', $review) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-label value="Estado" />
                        <select name="estado" class="select select-bordered w-full">
                            <option value="ativo">Aprovar</option>
                            <option value="recusado">Recusar</option>
                        </select>
                    </div>

                    <div>
                        <x-label value="Justificação (obrigatória se recusado)" />
                        <textarea
                            name="justificacao_recusa"
                            rows="4"
                            class="textarea textarea-bordered w-full"
                        >{{ old('justificacao_recusa') }}</textarea>

                        @error('justificacao_recusa')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline">
                            Voltar
                        </a>

                        <button class="btn btn-primary">
                            Guardar decisão
                        </button>
                    </div>
                </form>

            </div>
        @endif

    </div>
</x-app-layout>
