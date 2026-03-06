<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-3xl text-cyan-200">Requisições</h2>
    </x-slot>

    <div class="grid gap-3 md:grid-cols-3">
        <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-4">
            <p class="text-xs uppercase tracking-widest text-cyan-300"># Requisições Ativas</p>
            <p class="mt-2 text-3xl font-semibold">{{ $indicadores['ativas'] }}</p>
        </div>
        <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-4">
            <p class="text-xs uppercase tracking-widest text-cyan-300"># Requisições últimos 30 dias</p>
            <p class="mt-2 text-3xl font-semibold">{{ $indicadores['ultimos_30_dias'] }}</p>
        </div>
        <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-4">
            <p class="text-xs uppercase tracking-widest text-cyan-300"># Livros entregues hoje</p>
            <p class="mt-2 text-3xl font-semibold">{{ $indicadores['entregues_hoje'] }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('requisicoes.store') }}" class="mt-6 grid gap-3 rounded-xl border border-cyan-300/20 bg-slate-900/70 p-4 md:grid-cols-4">
        @csrf
        <div class="md:col-span-2">
            <x-label value="Livro disponível" />
            <select name="livro_id" class="select select-bordered w-full" required>
                <option value="">Selecione...</option>
                @foreach ($livrosDisponiveis as $livro)
                    <option value="{{ $livro->id }}" @selected(old('livro_id', $livroPreSelecionado) == $livro->id)>
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
                        <option value="{{ $cidadao->id }}" @selected(old('cidadao_id') == $cidadao->id)>{{ $cidadao->name }}</option>
                    @endforeach
                </select>
                <x-input-error for="cidadao_id" class="mt-1" />
            </div>
        @endif

        <div class="flex items-end">
            <button class="btn btn-primary w-full" type="submit">Requisitar</button>
        </div>
    </form>

    <div class="mt-6 overflow-x-auto rounded-xl border border-cyan-300/20 bg-slate-900/70">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Livro</th>
                    <th>Cidadão</th>
                    <th>Início</th>
                    <th>Fim previsto</th>
                    <th>Fim real</th>
                    <th>Dias</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($requisicoes as $requisicao)
                    <tr>
                        <td class="font-semibold">{{ $requisicao->numero_sequencial }}</td>
                        <td>
                            <a href="{{ route('catalogo.show', $requisicao->livro) }}" class="hover:underline">{{ $requisicao->livro?->nome }}</a>
                        </td>
                        <td>{{ $requisicao->cidadao_nome }}</td>
                        <td>{{ $requisicao->data_requisicao?->format('d/m/Y') }}</td>
                        <td>{{ $requisicao->data_prevista_entrega?->format('d/m/Y') }}</td>
                        <td>{{ $requisicao->data_real_entrega?->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ $requisicao->dias_decorridos ?? $requisicao->dias_em_aberto }}</td>
                        <td>
                            @if (auth()->user()->isAdmin() && ! $requisicao->data_real_entrega)
                                <form method="POST" action="{{ route('requisicoes.confirmar-entrega', $requisicao) }}" class="flex justify-end gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="date" name="data_real_entrega" value="{{ now()->format('Y-m-d') }}" class="input input-bordered input-sm" required />
                                    <button class="btn btn-success btn-sm" type="submit">Confirmar entrega</button>
                                </form>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Sem requisições.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $requisicoes->links() }}</div>
</x-app-layout>

