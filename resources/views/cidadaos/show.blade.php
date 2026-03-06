<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-3xl text-cyan-200">{{ $cidadao->name }}</h2>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-[260px_minmax(0,1fr)]">
        <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-4">
            @if ($cidadao->profile_photo_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($cidadao->profile_photo_path) }}" alt="Foto" class="w-full rounded-lg" />
            @else
                <div class="flex h-64 items-center justify-center rounded-lg border border-dashed border-cyan-300/30 text-slate-400">Sem foto</div>
            @endif
            <p class="mt-3 text-sm text-slate-300">{{ $cidadao->email }}</p>
        </div>

        <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5">
            <h3 class="font-display text-2xl text-cyan-200">Hist&oacute;rico de requisi&ccedil;&otilde;es</h3>
            <div class="mt-3 overflow-x-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Livro</th>
                            <th>In&iacute;cio</th>
                            <th>Fim previsto</th>
                            <th>Fim real</th>
                            <th>Dias</th>
                            <th class="text-right">A&ccedil;&otilde;es</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cidadao->requisicoes as $requisicao)
                            <tr>
                                <td>{{ $requisicao->numero_sequencial }}</td>
                                <td>{{ $requisicao->livro?->nome }}</td>
                                <td>{{ $requisicao->data_requisicao?->format('d/m/Y') }}</td>
                                <td>{{ $requisicao->data_prevista_entrega?->format('d/m/Y') }}</td>
                                <td>{{ $requisicao->data_real_entrega?->format('d/m/Y') ?? '-' }}</td>
                                <td>{{ $requisicao->dias_decorridos ?? $requisicao->dias_em_aberto }}</td>
                                <td>
                                    <div class="flex justify-end">
                                        @if (! $requisicao->data_real_entrega)
                                            <form method="POST" action="{{ route('requisicoes.confirmar-entrega', $requisicao) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="data_real_entrega" value="{{ now()->format('Y-m-d') }}" />
                                                <button type="submit" class="btn btn-success btn-sm">Devolver livro</button>
                                            </form>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Sem requisi&ccedil;&otilde;es.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
