<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-display text-3xl text-cyan-200">Carrinho</h2>

            <a href="{{ route('catalogo.index') }}" class="btn btn-outline btn-sm">
                Continuar a comprar
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-rose-400/30 bg-rose-500/10 px-4 py-3 text-rose-200">
                {{ session('error') }}
            </div>
        @endif

        @php
            $total = $cart->items->sum(function ($item) {
                return ((float) $item->livro->preco * $item->quantity);
            });
        @endphp

        @if ($cart->items->isEmpty())
            <div class="rounded-2xl border border-cyan-300/20 bg-slate-900/70 p-8 text-center">
                <p class="text-lg text-slate-200">O teu carrinho est&aacute; vazio.</p>
                <p class="mt-2 text-sm text-slate-400">Adiciona livros ao carrinho para avan&ccedil;ares para o checkout.</p>

                <div class="mt-6">
                    <a href="{{ route('catalogo.index') }}" class="btn btn-primary">
                        Explorar cat&aacute;logo
                    </a>
                </div>
            </div>
        @else
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
                <div class="rounded-2xl border border-cyan-300/20 bg-slate-900/70 p-5">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <h3 class="font-display text-2xl text-cyan-100">Livros adicionados</h3>
                            <p class="text-sm text-slate-400">
                                {{ $cart->items->count() }} {{ $cart->items->count() === 1 ? 'item' : 'itens' }} no carrinho
                            </p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @foreach ($cart->items as $item)
                            <div class="flex flex-col gap-4 rounded-xl border border-cyan-300/15 bg-slate-950/40 p-4 sm:flex-row">
                                <div class="w-full sm:w-24">
                                    @if ($item->livro->capa_imagem)
                                        <img
                                            src="{{ \Illuminate\Support\Facades\Storage::url($item->livro->capa_imagem) }}"
                                            alt="Capa de {{ $item->livro->nome }}"
                                            class="h-32 w-full rounded-lg object-cover sm:h-28"
                                        />
                                    @else
                                        <div class="flex h-32 w-full items-center justify-center rounded-lg border border-dashed border-cyan-300/20 text-xs text-slate-500 sm:h-28">
                                            Sem capa
                                        </div>
                                    @endif
                                </div>

                                <div class="flex min-w-0 flex-1 flex-col justify-between gap-3">
                                    <div>
                                        <h4 class="text-lg font-semibold text-cyan-100">{{ $item->livro->nome }}</h4>

                                        <p class="mt-1 text-sm text-slate-400">
                                            ISBN {{ $item->livro->isbn ?: '-' }}
                                        </p>

                                        <p class="mt-2 text-sm text-slate-300">
                                            Quantidade:
                                            <span class="font-medium text-cyan-200">{{ $item->quantity }}</span>
                                        </p>

                                        <p class="mt-1 text-sm text-slate-300">
                                            Pre&ccedil;o unit&aacute;rio:
                                            <span class="font-medium text-cyan-200">
                                                €{{ number_format((float) $item->livro->preco, 2, ',', '.') }}
                                            </span>
                                        </p>

                                        <p class="mt-1 text-sm text-slate-300">
                                            Subtotal:
                                            <span class="font-semibold text-cyan-100">
                                                €{{ number_format((float) $item->livro->preco * $item->quantity, 2, ',', '.') }}
                                            </span>
                                        </p>
                                    </div>

                                    <div class="flex flex-wrap gap-3">
                                        <a href="{{ route('catalogo.show', $item->livro) }}" class="btn btn-outline btn-sm">
                                            Ver detalhe
                                        </a>

                                        <form method="POST" action="{{ route('cart.destroy', $item->livro) }}">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-error btn-sm">
                                                Remover
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="h-fit rounded-2xl border border-cyan-300/20 bg-slate-900/70 p-5">
                    <h3 class="font-display text-2xl text-cyan-100">Resumo</h3>

                    <div class="mt-4 space-y-3 text-sm">
                        <div class="flex items-center justify-between text-slate-300">
                            <span>Total de itens</span>
                            <span>{{ $cart->items->sum('quantity') }}</span>
                        </div>

                        <div class="flex items-center justify-between text-slate-300">
                            <span>Total a pagar</span>
                            <span class="text-lg font-semibold text-cyan-100">
                                €{{ number_format($total, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <a href="{{ route('checkout.show') }}" class="btn btn-primary w-full">
                            Finalizar compra
                        </a>

                        <a href="{{ route('catalogo.index') }}" class="btn btn-outline w-full">
                            Adicionar mais livros
                        </a>
                    </div>

                    <p class="mt-4 text-xs leading-5 text-slate-400">
                        No checkout vais poder confirmar a morada de entrega e concluir o pagamento com Stripe.
                    </p>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
