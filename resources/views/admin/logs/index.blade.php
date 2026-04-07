<x-app-layout>
    <div class="space-y-6">
        <div class="rounded-[28px] border border-stone-300 bg-[#efe4cf] p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-stone-500">
                        Auditoria da aplicação
                    </p>
                    <h1 class="mt-2 text-4xl font-semibold tracking-tight text-[#5c3b24]">
                        Logs
                    </h1>
                    <p class="mt-2 max-w-2xl text-sm text-stone-600">
                        Consulte o histórico de ações realizadas na aplicação e acompanhe quem fez o quê.
                    </p>
                </div>

                <div class="rounded-2xl border border-stone-300 bg-[#f6edde] px-5 py-4 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.2em] text-stone-500">Total de registos</p>
                    <p class="mt-1 text-2xl font-semibold text-[#5c3b24]">{{ $logs->total() }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-[28px] border border-stone-300 bg-[#efe4cf] p-5 shadow-sm">
            <form method="GET" action="{{ route('admin.logs.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label for="module" class="mb-2 block text-sm font-medium text-[#5c3b24]">Módulo</label>
                    <select
                        name="module"
                        id="module"
                        class="w-full rounded-2xl border border-stone-300 bg-[#f6edde] px-4 py-3 text-sm text-stone-700 focus:border-stone-400 focus:outline-none"
                    >
                        <option value="">Todos</option>
                        @foreach ($modules as $module)
                            <option value="{{ $module }}" @selected(request('module') == $module)>
                                {{ $module }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="user" class="mb-2 block text-sm font-medium text-[#5c3b24]">Utilizador</label>
                    <input
                        type="text"
                        name="user"
                        id="user"
                        value="{{ request('user') }}"
                        placeholder="Pesquisar por nome"
                        class="w-full rounded-2xl border border-stone-300 bg-[#f6edde] px-4 py-3 text-sm text-stone-700 placeholder:text-stone-400 focus:border-stone-400 focus:outline-none"
                    >
                </div>

                <div class="flex items-end gap-3">
                    <button
                        type="submit"
                        class="rounded-2xl border border-[#5c3b24] bg-[#5c3b24] px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90"
                    >
                        Filtrar
                    </button>

                    <a
                        href="{{ route('admin.logs.index') }}"
                        class="rounded-2xl border border-stone-300 bg-[#f6edde] px-5 py-3 text-sm font-semibold text-[#5c3b24] transition hover:bg-[#eadcc3]"
                    >
                        Limpar
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-[28px] border border-stone-300 bg-[#efe4cf] p-5 shadow-sm">
            <div class="mb-4">
                <h2 class="text-2xl font-semibold text-[#5c3b24]">Registos</h2>
                <p class="mt-1 text-sm text-stone-600">
                    Histórico completo de ações registadas no sistema.
                </p>
            </div>

            <div class="overflow-hidden rounded-[24px] border border-stone-300 bg-[#f6edde]">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-[#eadcc3] text-[#5c3b24]">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Data</th>
                                <th class="px-4 py-3 font-semibold">Hora</th>
                                <th class="px-4 py-3 font-semibold">User</th>
                                <th class="px-4 py-3 font-semibold">Módulo</th>
                                <th class="px-4 py-3 font-semibold">ID do objeto</th>
                                <th class="px-4 py-3 font-semibold">Alteração</th>
                                <th class="px-4 py-3 font-semibold">IP</th>
                                <th class="px-4 py-3 font-semibold">Browser</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-300">
                            @forelse ($logs as $log)
                                <tr class="align-top">
                                    <td class="px-4 py-3 text-stone-700">
                                        {{ $log->created_at?->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-stone-700">
                                        {{ $log->created_at?->format('H:i:s') }}
                                    </td>
                                    <td class="px-4 py-3 text-stone-700">
                                        {{ $log->user?->name ?? 'Sistema' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full border border-stone-300 bg-[#efe4cf] px-3 py-1 text-xs font-semibold text-[#5c3b24]">
                                            {{ $log->module }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-stone-700">
                                        {{ $log->object_id ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-stone-700">
                                        <div class="space-y-1">
                                            <p class="font-semibold text-[#5c3b24]">{{ $log->action }}</p>
                                            @if ($log->description)
                                                <p class="text-xs text-stone-600">{{ $log->description }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-stone-700">
                                        {{ $log->ip_address ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-stone-700">
                                        <div class="max-w-[260px] break-words text-xs">
                                            {{ $log->user_agent ?? '—' }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-10 text-center">
                                        <p class="text-lg font-semibold text-[#5c3b24]">Ainda não existem logs registados.</p>
                                        <p class="mt-2 text-sm text-stone-600">
                                            Os registos vão aparecer aqui assim que começares a guardar ações no sistema.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
