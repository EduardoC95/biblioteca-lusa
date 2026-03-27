<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-3xl text-cyan-200">{{ $livro->nome }}</h2>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-[280px_minmax(0,1fr)]">
        <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-4">
            @if ($livro->capa_imagem)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($livro->capa_imagem) }}" alt="Capa" class="w-full rounded-lg border border-cyan-300/20 object-cover" />
            @else
                <div class="flex h-80 items-center justify-center rounded-lg border border-dashed border-cyan-300/30 text-slate-400">Sem capa</div>
            @endif
        </div>

        <div class="space-y-5">
            <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5">
                <h3 class="font-display text-xl text-cyan-200">Estado</h3>
                <p class="mt-2">
                    <span class="badge {{ $livro->requisicoes->firstWhere('data_real_entrega', null) ? 'badge-warning' : 'badge-success' }}">
                        {{ $livro->requisicoes->firstWhere('data_real_entrega', null) ? 'Indisponível para requisição' : 'Disponível para requisição' }}
                    </span>
                </p>
                <div class="mt-3">
                    <a href="{{ route('requisicoes.index', ['livro_id' => $livro->id]) }}" class="btn btn-primary">Requisitar</a>
                </div>
            </div>

            <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5">
                <h3 class="font-display text-xl text-cyan-200">Editora</h3>
                <p class="mt-2 text-slate-200">{{ $livro->editora?->nome }}</p>
            </div>

            <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5">
                <h3 class="font-display text-xl text-cyan-200">Autor / Autores</h3>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach ($livro->autores as $autor)
                        <a href="{{ route('autores.show', $autor) }}" class="badge badge-lg border-cyan-300/40 bg-slate-950 text-cyan-200 hover:bg-slate-800">{{ $autor->nome }}</a>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5">
                <h3 class="font-display text-xl text-cyan-200">Sinopse</h3>
                <p class="mt-3 whitespace-pre-line text-slate-200">{{ $livro->sinopse ?: 'Sem sinopse disponível.' }}</p>
            </div>

            @if(isset($relacionados) && $relacionados->isNotEmpty())
                <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5">
                    <h3 class="font-display text-xl text-cyan-200">Livros Relacionados</h3>
                    <p class="mt-2 text-sm text-slate-400">
                        Sugestões automáticas com base na semelhança entre descrições e conteúdo textual.
                    </p>

                    <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @foreach ($relacionados as $relacionado)
                            <a href="{{ route('livros.show', $relacionado) }}"
                               class="group rounded-xl border border-cyan-300/20 bg-slate-950/60 p-4 transition hover:border-cyan-300/40 hover:bg-slate-900">
                                <div class="mb-3">
                                    @if ($relacionado->capa_imagem)
                                        <img
                                            src="{{ \Illuminate\Support\Facades\Storage::url($relacionado->capa_imagem) }}"
                                            alt="Capa de {{ $relacionado->nome }}"
                                            class="h-48 w-full rounded-lg border border-cyan-300/20 object-cover"
                                        />
                                    @else
                                        <div class="flex h-48 items-center justify-center rounded-lg border border-dashed border-cyan-300/20 text-sm text-slate-500">
                                            Sem capa
                                        </div>
                                    @endif
                                </div>

                                <h4 class="line-clamp-2 font-semibold text-cyan-200 group-hover:text-cyan-100">
                                    {{ $relacionado->nome }}
                                </h4>

                                <p class="mt-2 text-sm text-slate-400">
                                    {{ $relacionado->editora?->nome ?? 'Editora indisponível' }}
                                </p>

                                <p class="mt-3 text-sm text-slate-300">
                                    {{ \Illuminate\Support\Str::limit($relacionado->sinopse ?: 'Sem sinopse disponível.', 120) }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5">
                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <p class="text-xs uppercase tracking-widest text-cyan-300">ISBN</p>
                        <p class="mt-1 text-slate-100">{{ $livro->isbn }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest text-cyan-300">Preço</p>
                        <p class="mt-1 text-slate-100">{{ number_format((float) $livro->preco, 2, ',', '.') }} EUR</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5">
                <h3 class="font-display text-xl text-cyan-200">Reviews</h3>

                @php
                    $reviewsAtivas = $livro->reviews->where('estado', 'ativo');
                @endphp

                @if($reviewsAtivas->count())
                    <div class="mt-4 space-y-4">
                        @foreach ($reviewsAtivas as $review)
                            <div class="rounded-lg border border-cyan-300/15 bg-slate-950/50 p-4">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <p class="font-semibold text-slate-100">{{ $review->user?->name ?? 'Cidadão' }}</p>

                                    @if($review->rating)
                                        <span class="badge badge-primary">{{ $review->rating }}/5</span>
                                    @endif
                                </div>

                                <p class="mt-3 whitespace-pre-line text-slate-200">{{ $review->comentario }}</p>

                                <p class="mt-3 text-xs text-slate-500">
                                    {{ $review->created_at?->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-3 text-slate-400">Ainda não existem reviews ativas para este livro.</p>
                @endif
            </div>

            <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5">
                <h3 class="font-display text-xl text-cyan-200">Histórico de requisições</h3>
                <div class="mt-3 overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cidadão</th>
                                <th>Início</th>
                                <th>Prevista</th>
                                <th>Real</th>
                                <th>Dias</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($livro->requisicoes as $requisicao)
                                <tr>
                                    <td>{{ $requisicao->numero_sequencial }}</td>
                                    <td>{{ $requisicao->cidadao_nome }}</td>
                                    <td>{{ $requisicao->data_requisicao?->format('d/m/Y') }}</td>
                                    <td>{{ $requisicao->data_prevista_entrega?->format('d/m/Y') }}</td>
                                    <td>{{ $requisicao->data_real_entrega?->format('d/m/Y') ?? '-' }}</td>
                                    <td>{{ $requisicao->dias_decorridos ?? $requisicao->dias_em_aberto }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Sem histórico.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('livros.index') }}" class="btn">Voltar</a>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('livros.edit', $livro) }}" class="btn btn-primary">Editar</a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
