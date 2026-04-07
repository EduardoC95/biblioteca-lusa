<x-app-layout>
    <div class="space-y-6">
        <div class="rounded-[28px] border border-stone-300 bg-[#efe4cf] p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-stone-500">
                        Gestão de compras
                    </p>
                    <h1 class="mt-2 text-4xl font-semibold tracking-tight text-[#5c3b24]">
                        Compras
                    </h1>
                    <p class="mt-2 max-w-2xl text-sm text-stone-600">
                        Consulte todas as compras registadas, acompanhe o estado da encomenda e valide ou recuse pagamentos manualmente quando necessário.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-stone-300 bg-[#f6edde] px-4 py-3 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.2em] text-stone-500">Total</p>
                        <p class="mt-1 text-2xl font-semibold text-[#5c3b24]">{{ $orders->total() }}</p>
                    </div>

                    <div class="rounded-2xl border border-stone-300 bg-[#f6edde] px-4 py-3 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.2em] text-stone-500">Pagas</p>
                        <p class="mt-1 text-2xl font-semibold text-[#5c3b24]">
                            {{ $orders->getCollection()->where('payment_status', \App\Models\Order::PAYMENT_PAID)->count() }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-stone-300 bg-[#f6edde] px-4 py-3 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.2em] text-stone-500">Pendentes</p>
                        <p class="mt-1 text-2xl font-semibold text-[#5c3b24]">
                            {{ $orders->getCollection()->where('payment_status', \App\Models\Order::PAYMENT_PENDING)->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-green-300 bg-green-50 px-4 py-3 text-sm font-medium text-green-700 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-[28px] border border-stone-300 bg-[#efe4cf] p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-[#5c3b24]">Lista de compras</h2>
                    <p class="mt-1 text-sm text-stone-600">
                        Visualize detalhes da encomenda, pagamento e livros incluídos.
                    </p>
                </div>
            </div>

            <div class="space-y-4">
                @forelse ($orders as $order)
                    <div class="rounded-[24px] border border-stone-300 bg-[#f6edde] p-5 shadow-sm">
                        <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                            <div class="grid flex-1 grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                                <div class="rounded-2xl border border-stone-300 bg-[#efe4cf] p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-stone-500">Compra</p>
                                    <p class="mt-2 text-2xl font-semibold text-[#5c3b24]">#{{ $order->id }}</p>
                                    <p class="mt-1 text-sm text-stone-600">
                                        {{ $order->created_at?->format('d/m/Y H:i') }}
                                    </p>
                                </div>

                                <div class="rounded-2xl border border-stone-300 bg-[#efe4cf] p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-stone-500">Utilizador</p>
                                    <p class="mt-2 text-lg font-semibold text-[#5c3b24]">
                                        {{ $order->user->name ?? '—' }}
                                    </p>
                                    <p class="mt-1 text-sm text-stone-600">
                                        {{ $order->delivery_email }}
                                    </p>
                                </div>

                                <div class="rounded-2xl border border-stone-300 bg-[#efe4cf] p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-stone-500">Total</p>
                                    <p class="mt-2 text-2xl font-semibold text-[#5c3b24]">
                                        {{ number_format(($order->total_amount ?? 0) / 100, 2, ',', '.') }} €
                                    </p>
                                    <p class="mt-1 text-sm text-stone-600">
                                        {{ strtoupper($order->currency ?? 'eur') }}
                                    </p>
                                </div>

                                <div class="rounded-2xl border border-stone-300 bg-[#efe4cf] p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-stone-500">Pagamento</p>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @if ($order->status === \App\Models\Order::STATUS_CONFIRMED)
                                            <span class="rounded-full border border-green-300 bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                                Encomenda confirmada
                                            </span>
                                        @elseif ($order->status === \App\Models\Order::STATUS_CANCELLED)
                                            <span class="rounded-full border border-red-300 bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                                Encomenda cancelada
                                            </span>
                                        @else
                                            <span class="rounded-full border border-yellow-300 bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                                Encomenda pendente
                                            </span>
                                        @endif

                                        @if ($order->payment_status === \App\Models\Order::PAYMENT_PAID)
                                            <span class="rounded-full border border-green-300 bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                                Pago
                                            </span>
                                        @elseif ($order->payment_status === \App\Models\Order::PAYMENT_FAILED)
                                            <span class="rounded-full border border-red-300 bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                                Falhado
                                            </span>
                                        @elseif ($order->payment_status === \App\Models\Order::PAYMENT_EXPIRED)
                                            <span class="rounded-full border border-stone-300 bg-stone-200 px-3 py-1 text-xs font-semibold text-stone-700">
                                                Expirado
                                            </span>
                                        @else
                                            <span class="rounded-full border border-yellow-300 bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                                Pendente
                                            </span>
                                        @endif
                                    </div>

                                    @if ($order->paid_at)
                                        <p class="mt-3 text-sm text-stone-600">
                                            Pago em {{ $order->paid_at->format('d/m/Y H:i') }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2 xl:w-[190px] xl:flex-col">
                                @if ($order->payment_status !== \App\Models\Order::PAYMENT_PAID)
                                    <form method="POST" action="{{ route('admin.orders.approve', $order) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="w-full rounded-2xl border border-green-700 bg-green-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-green-700"
                                        >
                                            Validar pagamento
                                        </button>
                                    </form>
                                @endif

                                @if ($order->status !== \App\Models\Order::STATUS_CANCELLED)
                                    <form method="POST" action="{{ route('admin.orders.reject', $order) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="w-full rounded-2xl border border-red-700 bg-red-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-red-700"
                                        >
                                            Recusar compra
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-[1.25fr_0.75fr]">
                            <div class="rounded-2xl border border-stone-300 bg-[#efe4cf] p-4">
                                <div class="mb-3 flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-[#5c3b24]">Livros incluídos</h3>
                                    <span class="rounded-full border border-stone-300 bg-[#f6edde] px-3 py-1 text-xs font-semibold text-stone-600">
                                        {{ $order->items->count() }} item(ns)
                                    </span>
                                </div>

                                <div class="space-y-3">
                                    @forelse ($order->items as $item)
                                        <div class="flex items-center justify-between rounded-2xl border border-stone-300 bg-[#f6edde] px-4 py-3">
                                            <div>
                                                <p class="font-semibold text-[#5c3b24]">{{ $item->book_title }}</p>
                                                <p class="text-sm text-stone-600">ISBN: {{ $item->book_isbn ?: '—' }}</p>
                                            </div>

                                            <div class="text-right">
                                                <p class="font-semibold text-[#5c3b24]">{{ $item->quantity }} x {{ number_format($item->unit_price / 100, 2, ',', '.') }} €</p>
                                                <p class="text-sm text-stone-600">
                                                    Subtotal: {{ number_format($item->subtotal / 100, 2, ',', '.') }} €
                                                </p>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-stone-500">Sem itens associados a esta compra.</p>
                                    @endforelse
                                </div>
                            </div>

                            <div class="rounded-2xl border border-stone-300 bg-[#efe4cf] p-4">
                                <h3 class="text-lg font-semibold text-[#5c3b24]">Entrega</h3>

                                <div class="mt-3 space-y-2 text-sm text-stone-700">
                                    <p><span class="font-semibold text-[#5c3b24]">Nome:</span> {{ $order->delivery_name }}</p>
                                    <p><span class="font-semibold text-[#5c3b24]">Email:</span> {{ $order->delivery_email }}</p>
                                    <p><span class="font-semibold text-[#5c3b24]">Telefone:</span> {{ $order->delivery_phone ?: '—' }}</p>
                                    <p><span class="font-semibold text-[#5c3b24]">Morada:</span> {{ $order->delivery_address }}</p>
                                    <p><span class="font-semibold text-[#5c3b24]">Código-postal:</span> {{ $order->delivery_postal_code }}</p>
                                    <p><span class="font-semibold text-[#5c3b24]">Cidade:</span> {{ $order->delivery_city }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-[24px] border border-stone-300 bg-[#f6edde] px-6 py-10 text-center shadow-sm">
                        <p class="text-lg font-semibold text-[#5c3b24]">Ainda não existem compras registadas.</p>
                        <p class="mt-2 text-sm text-stone-600">
                            Quando surgirem novas encomendas, vão aparecer aqui.
                        </p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
