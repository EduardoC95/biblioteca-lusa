<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-3xl text-cyan-200">Pagamento conclu&iacute;do</h2>
    </x-slot>

    <div class="rounded-2xl border border-emerald-400/30 bg-emerald-500/10 p-8 text-center">
        <h3 class="text-2xl font-semibold text-emerald-200">Obrigado pela tua compra.</h3>
        <p class="mt-3 text-slate-300">
            O teu pagamento foi processado com sucesso. A encomenda ser&aacute; atualizada automaticamente.
        </p>

        <div class="mt-6 flex justify-center gap-3">
            <a href="{{ route('catalogo.index') }}" class="btn btn-primary">
                Voltar ao cat&aacute;logo
            </a>

            <a href="{{ route('cart.index') }}" class="btn btn-outline">
                Ver carrinho
            </a>
        </div>
    </div>
</x-app-layout>
