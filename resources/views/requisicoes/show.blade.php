<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-3xl text-cyan-200">Detalhe da Requisição #{{ $requisicao->numero_sequencial }}</h2>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
        <div class="space-y-6">
            <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5">
                <h3 class="font-display text-xl text-cyan-200">Dados da requisição</h3>

                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div>
                        <p class="text-xs uppercase tracking-widest text-cyan-300">Livro</p>
                        <p class="mt-1 text-slate-100">
                            <a href="{{ route('catalogo.show', $requisicao->livro) }}" class="hover:underline">
                                {{ $requisicao->livro?->nome }}
                            </a>
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-widest text-cyan-300">Cidadão</p>
                        <p class="mt-1 text-slate-100">{{ $requisicao->cidadao_nome }}</p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-widest text-cyan-300">Data da requisição</p>
                        <p class="mt-1 text-slate-100">{{ $requisicao->data_requisicao?->format('d/m/Y') ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-widest text-cyan-300">Entrega prevista</p>
                        <p class="mt-1 text-slate-100">{{ $requisicao->data_entrega_prevista?->format('d/m/Y') ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-widest text-cyan-300">Devolução prevista</p>
                        <p class="mt-1 text-slate-100">{{ $requisicao->data_devolucao_prevista?->format('d/m/Y') ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-widest text-cyan-300">Devolução real</p>
                        <p class="mt-1 text-slate-100">{{ $requisicao->data_devolucao_real?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="rounded-xl border border-green-400/20 bg-green-500/10 p-4 text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-xl border border-red-400/20 bg-red-500/10 p-4 text-red-200">
                    {{ session('error') }}
                </div>
            @endif

            @if(auth()->user()->isAdmin())
                <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5">
                    <h3 class="font-display text-xl text-cyan-200">Gestão</h3>

                    <div class="mt-4">
                        @if (! $requisicao->data_entrega_real)
                            <form method="POST" action="{{ route('requisicoes.confirmar-entrega', $requisicao) }}" class="flex flex-wrap gap-3">
                                @csrf
                                @method('PATCH')

                                <input
                                    type="date"
                                    name="data_real_entrega"
                                    value="{{ now()->format('Y-m-d') }}"
                                    class="input input-bordered"
                                    required
                                />

                                <button class="btn btn-success">
                                    Confirmar entrega
                                </button>
                            </form>
                        @elseif (! $requisicao->data_devolucao_real)
                            <form method="POST" action="{{ route('requisicoes.confirmar-devolucao', $requisicao) }}" class="flex flex-wrap gap-3">
                                @csrf
                                @method('PATCH')

                                <input
                                    type="date"
                                    name="data_devolucao_real"
                                    value="{{ now()->format('Y-m-d') }}"
                                    class="input input-bordered"
                                    required
                                />

                                <button class="btn btn-warning">
                                    Confirmar devolução
                                </button>
                            </form>
                        @else
                            <div class="rounded-lg border border-green-400/20 bg-green-500/10 p-4 text-green-200">
                                Requisição concluída.
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if(
                auth()->check() &&
                !auth()->user()->isAdmin() &&
                $requisicao->cidadao_id === auth()->id() &&
                $requisicao->data_devolucao_real &&
                !$requisicao->review
            )
                <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5">
                    <h3 class="font-display text-xl text-cyan-200">Deixar review</h3>
                    <p class="mt-2 text-sm text-slate-400">
                        O seu comentário será enviado para moderação antes de ficar visível no livro.
                    </p>

                    <form method="POST" action="{{ route('reviews.store', $requisicao) }}" class="mt-4 space-y-4">
                        @csrf

                        <div>
                            <x-label for="rating" value="Classificação (1 a 5)" />
                            <select name="rating" id="rating" class="select select-bordered w-full">
                                <option value="">Sem classificação</option>
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" @selected(old('rating') == $i)>{{ $i }}</option>
                                @endfor
                            </select>
                            <x-input-error for="rating" class="mt-1" />
                        </div>

                        <div>
                            <x-label for="comentario" value="Comentário" />
                            <textarea
                                name="comentario"
                                id="comentario"
                                rows="5"
                                class="textarea textarea-bordered w-full"
                                required
                            >{{ old('comentario') }}</textarea>
                            <x-input-error for="comentario" class="mt-1" />
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="btn btn-primary">
                                Submeter review
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            @if($requisicao->review)
                <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5">
                    <h3 class="font-display text-xl text-cyan-200">O seu review</h3>

                    <div class="mt-4 space-y-3">
                        <div>
                            <p class="text-xs uppercase tracking-widest text-cyan-300">Estado</p>
                            <p class="mt-1">
                                @if($requisicao->review->estado === 'suspenso')
                                    <span class="badge badge-warning">Suspenso</span>
                                @elseif($requisicao->review->estado === 'ativo')
                                    <span class="badge badge-success">Ativo</span>
                                @else
                                    <span class="badge badge-error">Recusado</span>
                                @endif
                            </p>
                        </div>

                        @if($requisicao->review->rating)
                            <div>
                                <p class="text-xs uppercase tracking-widest text-cyan-300">Classificação</p>
                                <p class="mt-1 text-slate-100">{{ $requisicao->review->rating }}/5</p>
                            </div>
                        @endif

                        <div>
                            <p class="text-xs uppercase tracking-widest text-cyan-300">Comentário</p>
                            <p class="mt-1 whitespace-pre-line text-slate-200">{{ $requisicao->review->comentario }}</p>
                        </div>

                        @if($requisicao->review->estado === 'recusado' && $requisicao->review->justificacao_recusa)
                            <div>
                                <p class="text-xs uppercase tracking-widest text-cyan-300">Justificação da recusa</p>
                                <p class="mt-1 whitespace-pre-line text-red-300">{{ $requisicao->review->justificacao_recusa }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5">
                <h3 class="font-display text-xl text-cyan-200">Livro</h3>

                <div class="mt-4 flex gap-4">
                    <div class="w-24 shrink-0">
                        @if ($requisicao->livro?->capa_imagem)
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::url($requisicao->livro->capa_imagem) }}"
                                alt="Capa"
                                class="w-full rounded-lg border border-cyan-300/20 object-cover"
                            />
                        @else
                            <div class="flex h-32 items-center justify-center rounded-lg border border-dashed border-cyan-300/30 text-xs text-slate-400">
                                Sem capa
                            </div>
                        @endif
                    </div>

                    <div class="min-w-0">
                        <p class="font-semibold text-slate-100">{{ $requisicao->livro?->nome }}</p>
                        <p class="mt-2 text-sm text-slate-400">ISBN: {{ $requisicao->livro?->isbn ?? '-' }}</p>

                        <div class="mt-4">
                            <a href="{{ route('catalogo.show', $requisicao->livro) }}" class="btn btn-sm btn-outline">
                                Ver livro
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('requisicoes.index') }}" class="btn">Voltar</a>
            </div>
        </div>
    </div>
</x-app-layout>
