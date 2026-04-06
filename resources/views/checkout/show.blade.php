<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-display text-3xl text-cyan-200">Checkout</h2>

            <a href="{{ route('cart.index') }}" class="btn btn-outline btn-sm">
                Voltar ao carrinho
            </a>
        </div>
    </x-slot>

    @php
        $total = $cart->items->sum(function ($item) {
            return ((float) $item->livro->preco * $item->quantity);
        });
    @endphp

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px]">
        <div class="rounded-2xl border border-cyan-300/20 bg-slate-900/70 p-5">
            <h3 class="font-display text-2xl text-cyan-100">Morada de entrega</h3>
            <p class="mt-2 text-sm text-slate-400">
                Confirma os teus dados antes de avan&ccedil;ares para o pagamento.
            </p>

            <form method="POST" action="{{ route('checkout.process') }}" class="mt-6 space-y-5">
                @csrf

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="delivery_name" class="mb-2 block text-sm font-medium text-slate-200">
                            Nome completo
                        </label>
                        <input
                            id="delivery_name"
                            name="delivery_name"
                            type="text"
                            value="{{ old('delivery_name', auth()->user()->name) }}"
                            class="input input-bordered w-full"
                            required
                        />
                        @error('delivery_name')
                            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="delivery_email" class="mb-2 block text-sm font-medium text-slate-200">
                            Email
                        </label>
                        <input
                            id="delivery_email"
                            name="delivery_email"
                            type="email"
                            value="{{ old('delivery_email', auth()->user()->email) }}"
                            class="input input-bordered w-full"
                            required
                        />
                        @error('delivery_email')
                            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="delivery_phone" class="mb-2 block text-sm font-medium text-slate-200">
                            Telefone
                        </label>
                        <input
                            id="delivery_phone"
                            name="delivery_phone"
                            type="text"
                            value="{{ old('delivery_phone') }}"
                            class="input input-bordered w-full"
                        />
                        @error('delivery_phone')
                            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="delivery_postal_code" class="mb-2 block text-sm font-medium text-slate-200">
                            C&oacute;digo postal
                        </label>
                        <input
                            id="delivery_postal_code"
                            name="delivery_postal_code"
                            type="text"
                            value="{{ old('delivery_postal_code') }}"
                            class="input input-bordered w-full"
                            required
                        />
                        @error('delivery_postal_code')
                            <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="delivery_address" class="mb-2 block text-sm font-medium text-slate-200">
                        Morada
                    </label>
                    <input
                        id="delivery_address"
                        name="delivery_address"
                        type="text"
                        value="{{ old('delivery_address') }}"
                        class="input input-bordered w-full"
                        required
                    />
                    @error('delivery_address')
                        <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="delivery_city" class="mb-2 block text-sm font-medium text-slate-200">
                        Cidade
                    </label>
                    <input
                        id="delivery_city"
                        name="delivery_city"
                        type="text"
                        value="{{ old('delivery_city') }}"
                        class="input input-bordered w-full"
                        required
                    />
                    @error('delivery_city')
                        <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <button type="submit" class="btn btn-primary">
                        Continuar para pagamento
                    </button>

                    <a href="{{ route('cart.index') }}" class="btn btn-outline">
                        Voltar
                    </a>
                </div>
            </form>
        </div>

        <div class="h-fit rounded-2xl border border-cyan-300/20 bg-slate-900/70 p-5">
            <h3 class="font-display text-2xl text-cyan-100">Resumo da encomenda</h3>

            <div class="mt-4 space-y-4">
                @foreach ($cart->items as $item)
                    <div class="rounded-xl border border-cyan-300/15 bg-slate-950/40 p-3">
                        <p class="font-medium text-cyan-100">{{ $item->livro->nome }}</p>
                        <p class="mt-1 text-sm text-slate-400">
                            Quantidade: {{ $item->quantity }}
                        </p>
                        <p class="mt-1 text-sm text-slate-300">
                            €{{ number_format((float) $item->livro->preco * $item->quantity, 2, ',', '.') }}
                        </p>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 border-t border-cyan-300/15 pt-4">
                <div class="flex items-center justify-between text-slate-300">
                    <span>Total de itens</span>
                    <span>{{ $cart->items->sum('quantity') }}</span>
                </div>

                <div class="mt-3 flex items-center justify-between">
                    <span class="text-slate-300">Total a pagar</span>
                    <span class="text-xl font-semibold text-cyan-100">
                        €{{ number_format($total, 2, ',', '.') }}
                    </span>
                </div>
            </div>

            <p class="mt-4 text-xs leading-5 text-slate-400">
                Ser&aacute;s redirecionado para a Stripe para concluir o pagamento em ambiente de teste.
            </p>
        </div>
    </div>
</x-app-layout>
