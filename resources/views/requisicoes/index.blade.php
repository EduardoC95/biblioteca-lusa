<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-3xl text-cyan-200">Requisições</h2>
    </x-slot>

    <div class="grid gap-3 md:grid-cols-3">
        <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-4">
            <p class="text-xs uppercase tracking-widest text-cyan-300">Requisições Ativas</p>
            <p class="mt-2 text-3xl font-semibold">{{ $indicadores['ativas'] }}</p>
        </div>

        <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-4">
            <p class="text-xs uppercase tracking-widest text-cyan-300">Requisições últimos 30 dias</p>
            <p class="mt-2 text-3xl font-semibold">{{ $indicadores['ultimos_30_dias'] }}</p>
        </div>

        <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-4">
            <p class="text-xs uppercase tracking-widest text-cyan-300">Livros devolvidos hoje</p>
            <p class="mt-2 text-3xl font-semibold">{{ $indicadores['entregues_hoje'] }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('requisicoes.store') }}"
        class="mt-6 grid gap-3 rounded-xl border border-cyan-300/20 bg-slate-900/70 p-4 md:grid-cols-4">

        @csrf

        <div class="md:col-span-2">
            <x-label value="Livro disponível" />

            <select name="livro_id" class="select select-bordered w-full" required>
                <option value="">Selecione...</option>

                @foreach ($livrosDisponiveis as $livro)
                    <option value="{{ $livro->id }}"
                        @selected(old('livro_id', $livroPreSelecionado) == $livro->id)>
                        #{{ $livro->id }} - {{ $livro->nome }}
                    </option>
                @endforeach
            </select>

            <x-input-error for="livro_id" class="mt-1" />
        </div>

        @if (auth()->user()->isAdmin())
            <div class="md:col-span-1">
                <x-label value="Cidadão" />

                <select name="cidadao_id" class="select select-bordered w-full" required>
                    <option value="">Selecione...</option>

                    @foreach ($cidadaos as $cidadao)
                        <option value="{{ $cidadao->id }}"
                            @selected(old('cidadao_id') == $cidadao->id)>
                            {{ $cidadao->name }}
                        </option>
                    @endforeach
                </select>

                <x-input-error for="cidadao_id" class="mt-1" />
            </div>
        @endif

        <div class="flex items-end">
            <button class="btn btn-primary w-full" type="submit">
                Requisitar
            </button>
        </div>
    </form>

    <div class="mt-6 overflow-hidden rounded-xl border border-cyan-300/20 bg-slate-900/70">
        <table class="table w-full text-sm">
            <thead>
                <tr class="text-cyan-300">
                    <th>#</th>
                    <th class="w-[180px]">Livro</th>
                    <th class="w-[120px]">Cidadão</th>
                    <th>Req.</th>
                    <th>Entrega</th>
                    <th>Devolução</th>
                    <th>Dias</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($requisicoes as $requisicao)
                    <tr class="hover:bg-slate-800/40">
                        <td class="font-semibold">
                            {{ $requisicao->numero_sequencial }}
                        </td>

                        <td class="max-w-[180px] truncate">
                            <a href="{{ route('catalogo.show', $requisicao->livro) }}" class="hover:underline">
                                {{ $requisicao->livro?->nome }}
                            </a>
                        </td>

                        <td class="max-w-[120px] truncate">
                            {{ $requisicao->cidadao_nome }}
                        </td>

                        <td>
                            {{ $requisicao->data_requisicao?->format('d/m/Y') }}
                        </td>

                        <td class="text-xs">
                            @if ($requisicao->data_entrega_real)
                                <span class="font-semibold text-green-400">
                                    {{ $requisicao->data_entrega_real->format('d/m/Y') }}
                                </span>
                                <div class="text-slate-400">real</div>
                            @else
                                <span>
                                    {{ $requisicao->data_entrega_prevista?->format('d/m/Y') ?? '-' }}
                                </span>
                                <div class="text-slate-500">prev.</div>
                            @endif
                        </td>

                        <td class="text-xs">
                            @if ($requisicao->data_devolucao_real)
                                <span class="font-semibold text-green-400">
                                    {{ $requisicao->data_devolucao_real->format('d/m/Y') }}
                                </span>
                                <div class="text-slate-400">real</div>
                            @else
                                <span class="{{ $requisicao->data_devolucao_prevista && now()->gt($requisicao->data_devolucao_prevista) ? 'font-semibold text-red-400' : '' }}">
                                    {{ $requisicao->data_devolucao_prevista?->format('d/m/Y') ?? '-' }}
                                </span>

                                <div class="text-slate-500">
                                    prev.
                                </div>
                            @endif
                        </td>

                        <td>
                            @if ($requisicao->data_devolucao_real)
                                <span class="font-semibold text-green-400">
                                    {{ $requisicao->dias_decorridos }}
                                </span>
                            @else
                                <span class="{{ $requisicao->data_devolucao_prevista && now()->gt($requisicao->data_devolucao_prevista) ? 'font-semibold text-red-400' : '' }}">
                                    {{ $requisicao->dias_em_aberto }}
                                </span>
                            @endif
                        </td>

                        <td class="text-right">
                            <div class="flex flex-wrap justify-end gap-2">
                                <a href="{{ route('requisicoes.show', $requisicao) }}" class="btn btn-sm">
                                    Ver detalhe
                                </a>

                                @if (auth()->user()->isAdmin())
                                    @if (! $requisicao->data_entrega_real)
                                        <form method="POST"
                                            action="{{ route('requisicoes.confirmar-entrega', $requisicao) }}"
                                            class="flex justify-end gap-2">

                                            @csrf
                                            @method('PATCH')

                                            <input
                                                type="date"
                                                name="data_real_entrega"
                                                value="{{ now()->format('Y-m-d') }}"
                                                class="input input-bordered input-sm w-[130px]"
                                                required
                                            />

                                            <button class="btn btn-success btn-sm">
                                                Entregar
                                            </button>
                                        </form>
                                    @elseif (! $requisicao->data_devolucao_real)
                                        <form method="POST"
                                            action="{{ route('requisicoes.confirmar-devolucao', $requisicao) }}"
                                            class="flex justify-end gap-2">

                                            @csrf
                                            @method('PATCH')

                                            <input
                                                type="date"
                                                name="data_devolucao_real"
                                                value="{{ now()->format('Y-m-d') }}"
                                                class="input input-bordered input-sm w-[130px]"
                                                required
                                            />

                                            <button class="btn btn-warning btn-sm">
                                                Devolver
                                            </button>
                                        </form>
                                    @else
                                        <span class="font-semibold text-green-400">
                                            Concluído
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-6 text-center">
                            Sem requisições.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $requisicoes->links() }}
    </div>
</x-app-layout>
