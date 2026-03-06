<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-3xl text-cyan-200">Cidadãos</h2>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5">
            <h3 class="font-display text-2xl text-cyan-200">Criar novo Admin</h3>
            <form method="POST" action="{{ route('admins.store') }}" class="mt-4 space-y-3">
                @csrf
                <div>
                    <x-label for="name" value="Nome" />
                    <x-input id="name" name="name" type="text" class="mt-1 w-full" required />
                    <x-input-error for="name" class="mt-1" />
                </div>
                <div>
                    <x-label for="email" value="Email" />
                    <x-input id="email" name="email" type="email" class="mt-1 w-full" required />
                    <x-input-error for="email" class="mt-1" />
                </div>
                <div>
                    <x-label for="password" value="Password" />
                    <x-input id="password" name="password" type="password" class="mt-1 w-full" required />
                    <x-input-error for="password" class="mt-1" />
                </div>
                <div>
                    <x-label for="password_confirmation" value="Confirmar password" />
                    <x-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 w-full" required />
                </div>
                <button type="submit" class="btn btn-primary">Criar Admin</button>
            </form>
        </section>

        <section class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-5">
            <h3 class="font-display text-2xl text-cyan-200">Lista de Cidadãos</h3>
            <div class="mt-3 space-y-2">
                @forelse ($cidadaos as $cidadao)
                    <a href="{{ route('cidadaos.show', $cidadao) }}" class="flex items-center justify-between rounded-lg border border-cyan-300/20 bg-slate-950/50 p-3 hover:border-cyan-300/40">
                        <span>{{ $cidadao->name }}</span>
                        <span class="text-sm text-slate-400">Ativas: {{ $cidadao->requisicoes_ativas_count }}</span>
                    </a>
                @empty
                    <p class="text-slate-400">Sem cidadãos registados.</p>
                @endforelse
            </div>
            <div class="mt-4">{{ $cidadaos->links() }}</div>
        </section>
    </div>
</x-app-layout>
