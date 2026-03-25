<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-3xl text-cyan-200">Reviews</h2>
    </x-slot>

    <div class="space-y-4">

        {{-- FILTRO --}}
        <form method="GET" class="flex flex-wrap gap-3 rounded-xl border border-cyan-300/20 bg-slate-900/70 p-4">
            <select name="estado" class="select select-bordered">
                <option value="">Todos os estados</option>
                <option value="suspenso" @selected($estado === 'suspenso')>Suspenso</option>
                <option value="ativo" @selected($estado === 'ativo')>Ativo</option>
                <option value="recusado" @selected($estado === 'recusado')>Recusado</option>
            </select>

            <button class="btn btn-primary">Filtrar</button>
        </form>

        {{-- TABELA --}}
        <div class="overflow-hidden rounded-xl border border-cyan-300/20 bg-slate-900/70">
            <table class="table w-full text-sm">
                <thead>
                    <tr class="text-cyan-300">
                        <th>#</th>
                        <th>Livro</th>
                        <th>Cidadão</th>
                        <th>Rating</th>
                        <th>Estado</th>
                        <th>Data</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($reviews as $review)
                        <tr class="hover:bg-slate-800/40">

                            <td>{{ $review->id }}</td>

                            <td class="max-w-[180px] truncate">
                                {{ $review->livro?->nome }}
                            </td>

                            <td class="max-w-[150px] truncate">
                                {{ $review->user?->name }}
                            </td>

                            <td>
                                {{ $review->rating ? $review->rating . '/5' : '-' }}
                            </td>

                            <td>
                                @if($review->estado === 'suspenso')
                                    <span class="badge badge-warning">Suspenso</span>
                                @elseif($review->estado === 'ativo')
                                    <span class="badge badge-success">Ativo</span>
                                @else
                                    <span class="badge badge-error">Recusado</span>
                                @endif
                            </td>

                            <td class="text-xs">
                                {{ $review->created_at?->format('d/m/Y H:i') }}
                            </td>

                            <td class="text-right">
                                <a href="{{ route('admin.reviews.show', $review) }}"
                                   class="btn btn-sm btn-outline">
                                    Ver
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6">
                                Sem reviews.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $reviews->links() }}
        </div>

    </div>
</x-app-layout>
