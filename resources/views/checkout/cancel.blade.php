<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-3xl text-cyan-200">Pagamento cancelado</h2>
    </x-slot>

    <div class="rounded-2xl border border-amber-400/30 bg-amber-500/10 p-8 text-center">
        <h3 class="text-2xl font-semibold text-amber-200">O pagamento n&atilde;o foi conclu&iacute;do.</h3>
        <p class="mt-3 text-slate-300">
            N&atilde;o te preocupes, os livros continuam no teu carrinho para tentares novamente.
        </p>

        <div class="mt-6 flex justify-center gap-3">
            <a href="{{ route('checkout.show') }}" class="btn btn-primary">
                Tentar novamente
            </a>

            <a href="{{ route('cart.index') }}" class="btn btn-outline">
                Voltar ao carrinho
            </a>
        </div>
    </div>
</x-app-layout>
