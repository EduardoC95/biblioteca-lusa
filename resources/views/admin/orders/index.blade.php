<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-semibold text-stone-800">Compras</h1>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-stone-300 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-stone-100 text-stone-700">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Utilizador</th>
                            <th class="px-4 py-3">Email entrega</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Estado encomenda</th>
                            <th class="px-4 py-3">Estado pagamento</th>
                            <th class="px-4 py-3">Data</th>
                            <th class="px-4 py-3">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200">
                        @forelse ($orders as $order)
                            <tr class="align-top">
                                <td class="px-4 py-3 font-semibold text-stone-800">#{{ $order->id }}</td>
                                <td class="px-4 py-3 text-stone-700">{{ $order->user->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-stone-700">{{ $order->delivery_email }}</td>
                                <td class="px-4 py-3 text-stone-700">
                                    {{ number_format(($order->total_amount ?? 0) / 100, 2, ',', '.') }} €
                                </td>
                                <td class="px-4 py-3">
                                    @if ($order->status === \App\Models\Order::STATUS_CONFIRMED)
                                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                            Confirmada
                                        </span>
                                    @elseif ($order->status === \App\Models\Order::STATUS_CANCELLED)
                                        <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                            Cancelada
                                        </span>
                                    @else
                                        <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                            Pendente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($order->payment_status === \App\Models\Order::PAYMENT_PAID)
                                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                            Pago
                                        </span>
                                    @elseif ($order->payment_status === \App\Models\Order::PAYMENT_FAILED)
                                        <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                            Falhado
                                        </span>
                                    @elseif ($order->payment_status === \App\Models\Order::PAYMENT_EXPIRED)
                                        <span class="rounded-full bg-stone-200 px-3 py-1 text-xs font-semibold text-stone-700">
                                            Expirado
                                        </span>
                                    @else
                                        <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                            Pendente
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-stone-700">
                                    {{ $order->created_at?->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        @if ($order->payment_status !== \App\Models\Order::PAYMENT_PAID)
                                            <form method="POST" action="{{ route('admin.orders.approve', $order) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    class="rounded-lg bg-green-600 px-3 py-2 text-xs font-semibold text-white hover:bg-green-700"
                                                >
                                                    Validar
                                                </button>
                                            </form>
                                        @endif

                                        @if ($order->status !== \App\Models\Order::STATUS_CANCELLED)
                                            <form method="POST" action="{{ route('admin.orders.reject', $order) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    class="rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700"
                                                >
                                                    Recusar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            @if ($order->items->isNotEmpty())
                                <tr>
                                    <td colspan="8" class="bg-stone-50 px-4 pb-4 pt-0">
                                        <div class="rounded-xl border border-stone-200 bg-white p-3">
                                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-stone-500">
                                                Livros da compra
                                            </p>

                                            <div class="space-y-2">
                                                @foreach ($order->items as $item)
                                                    <div class="flex items-center justify-between rounded-lg border border-stone-200 px-3 py-2 text-sm text-stone-700">
                                                        <span>{{ $item->book_title }}</span>
                                                        <span>{{ $item->quantity }} x {{ number_format($item->unit_price / 100, 2, ',', '.') }} €</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-stone-500">
                                    Ainda não existem compras registadas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            {{ $orders->links() }}
        </div>
    </div>
</x-app-layout>
