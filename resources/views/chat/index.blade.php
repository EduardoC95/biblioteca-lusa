<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-display text-3xl text-cyan-200">Chat Interno</h2>

            @if(auth()->user()->isAdmin())
                <a
                    href="{{ route('chat.rooms.create') }}"
                    class="inline-flex items-center rounded-lg border border-cyan-300/30 bg-cyan-400/10 px-4 py-2 text-sm font-semibold text-cyan-100 transition hover:bg-cyan-400/20"
                >
                    Nova sala
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid min-h-[70vh] gap-6 lg:grid-cols-[340px_minmax(0,1fr)]">

                {{-- Sidebar --}}
                <aside class="overflow-hidden rounded-2xl border border-cyan-300/20 bg-slate-900/70 shadow-xl">
                    <div class="border-b border-cyan-300/10 px-5 py-4">
                        <p class="text-xs uppercase tracking-[0.3em] text-cyan-300/70">Biblioteca Lusa</p>
                        <h3 class="mt-1 text-xl font-semibold text-white">Conversas</h3>
                        <p class="mt-1 text-sm text-slate-400">
                            Mensagens diretas e salas da equipa
                        </p>
                    </div>

                    {{-- Iniciar mensagem direta --}}
                    <div class="border-b border-cyan-300/10 px-5 py-4">
                        <h4 class="mb-3 text-sm font-semibold text-cyan-100">Nova mensagem direta</h4>

                        <div class="max-h-56 space-y-2 overflow-y-auto pr-1">
                            @forelse($teamMembers as $member)
                                <form method="POST" action="{{ route('chat.direct.start', $member) }}">
                                    @csrf

                                    <button
                                        type="submit"
                                        class="flex w-full items-center gap-3 rounded-xl border border-transparent bg-slate-800/80 px-3 py-3 text-left transition hover:border-cyan-300/20 hover:bg-slate-800"
                                    >
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-cyan-400/15 text-sm font-bold text-cyan-200">
                                            {{ strtoupper(mb_substr($member->name, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-medium text-white">{{ $member->name }}</p>
                                            <p class="truncate text-xs text-slate-400">{{ $member->email }}</p>
                                        </div>
                                    </button>
                                </form>
                            @empty
                                <div class="rounded-xl border border-cyan-300/10 bg-slate-800/70 px-3 py-3 text-sm text-slate-400">
                                    Não existem outros utilizadores ativos disponíveis.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Lista de conversas --}}
                    <div class="px-3 py-4">
                        <h4 class="mb-3 px-2 text-sm font-semibold text-cyan-100">As tuas conversas</h4>

                        <div class="space-y-2">
                            @forelse($conversations as $conversation)
                                @php
                                    $isActive = $activeConversation?->id === $conversation->id;

                                    if ($conversation->type === 'room') {
                                        $title = $conversation->room?->name ?? 'Sala';
                                        $subtitle = 'Sala · #' . ($conversation->room?->reference ?? 'sem-referência');
                                        $avatar = strtoupper(mb_substr($title, 0, 1));
                                    } else {
                                        $otherUser = $conversation->users->firstWhere('id', '!=', auth()->id());
                                        $title = $otherUser?->name ?? 'Mensagem direta';
                                        $subtitle = $otherUser?->email ?? 'Conversa privada';
                                        $avatar = strtoupper(mb_substr($title, 0, 1));
                                    }

                                    $latestBody = $conversation->latestMessage?->body;
                                @endphp

                                <a
                                    href="{{ route('chat.show', $conversation) }}"
                                    class="block rounded-2xl border px-3 py-3 transition {{ $isActive
                                        ? 'border-cyan-300/30 bg-cyan-400/10 shadow-lg'
                                        : 'border-transparent bg-slate-800/70 hover:border-cyan-300/15 hover:bg-slate-800'
                                    }}"
                                >
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-cyan-400/15 text-sm font-bold text-cyan-200">
                                            {{ $avatar }}
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="truncate text-sm font-semibold text-white">{{ $title }}</p>

                                                @if($conversation->latestMessage)
                                                    <span class="shrink-0 text-[11px] text-slate-400">
                                                        {{ $conversation->latestMessage->created_at->format('H:i') }}
                                                    </span>
                                                @endif
                                            </div>

                                            <p class="truncate text-xs text-slate-400">{{ $subtitle }}</p>

                                            @if($latestBody)
                                                <p class="mt-1 truncate text-sm text-slate-300">
                                                    {{ $latestBody }}
                                                </p>
                                            @else
                                                <p class="mt-1 truncate text-sm italic text-slate-500">
                                                    Sem mensagens ainda
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="rounded-2xl border border-cyan-300/10 bg-slate-800/70 px-4 py-4 text-sm text-slate-400">
                                    Ainda não tens conversas. Inicia uma mensagem direta ou cria uma sala.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </aside>

                {{-- Painel principal --}}
                <section class="flex min-h-[70vh] flex-col overflow-hidden rounded-2xl border border-cyan-300/20 bg-slate-900/70 shadow-xl">
                    @if(session('success'))
                        <div class="border-b border-emerald-400/20 bg-emerald-500/10 px-5 py-3 text-sm text-emerald-300">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($activeConversation)
                        @php
                            if ($activeConversation->type === 'room') {
                                $headerTitle = $activeConversation->room?->name ?? 'Sala';
                                $headerSubtitle = 'Sala de equipa';
                                $headerAvatar = strtoupper(mb_substr($headerTitle, 0, 1));
                            } else {
                                $otherUser = $activeConversation->users->firstWhere('id', '!=', auth()->id());
                                $headerTitle = $otherUser?->name ?? 'Mensagem direta';
                                $headerSubtitle = $otherUser?->email ?? 'Conversa privada';
                                $headerAvatar = strtoupper(mb_substr($headerTitle, 0, 1));
                            }
                        @endphp

                        {{-- Header da conversa --}}
                        <div class="border-b border-cyan-300/10 px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-cyan-400/15 text-sm font-bold text-cyan-200">
                                    {{ $headerAvatar }}
                                </div>

                                <div class="min-w-0">
                                    <h3 class="truncate text-lg font-semibold text-white">{{ $headerTitle }}</h3>
                                    <p class="truncate text-sm text-slate-400">{{ $headerSubtitle }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Mensagens --}}
                        <div class="flex-1 space-y-4 overflow-y-auto px-5 py-5">
                            @forelse($activeConversation->messagesOldestFirst as $message)
                                @php
                                    $isMine = $message->user_id === auth()->id();
                                @endphp

                                <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-2xl rounded-2xl border px-4 py-3 shadow-sm {{ $isMine
                                        ? 'border-cyan-300/20 bg-cyan-500/15 text-cyan-50'
                                        : 'border-slate-700 bg-slate-800/90 text-slate-100'
                                    }}">
                                        <div class="mb-1 flex items-center gap-2">
                                            <span class="text-xs font-semibold {{ $isMine ? 'text-cyan-200' : 'text-slate-300' }}">
                                                {{ $message->user->name }}
                                            </span>
                                            <span class="text-[11px] {{ $isMine ? 'text-cyan-300/80' : 'text-slate-500' }}">
                                                {{ $message->created_at->format('d/m H:i') }}
                                            </span>
                                        </div>

                                        <p class="whitespace-pre-wrap text-sm leading-6">
                                            {{ $message->body }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="flex h-full items-center justify-center">
                                    <div class="rounded-2xl border border-cyan-300/10 bg-slate-800/70 px-6 py-8 text-center">
                                        <p class="text-lg font-semibold text-white">Ainda não há mensagens</p>
                                        <p class="mt-2 text-sm text-slate-400">
                                            Envia a primeira mensagem desta conversa.
                                        </p>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        {{-- Composer --}}
                        <div class="border-t border-cyan-300/10 bg-slate-950/30 px-5 py-4">
                            <form method="POST" action="{{ route('chat.messages.store', $activeConversation) }}" class="flex items-end gap-3">
                                @csrf

                                <div class="flex-1">
                                    <label for="body" class="sr-only">Mensagem</label>
                                    <textarea
                                        id="body"
                                        name="body"
                                        rows="2"
                                        class="w-full rounded-2xl border border-cyan-300/10 bg-slate-800 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-cyan-300/30 focus:outline-none focus:ring-2 focus:ring-cyan-400/20"
                                        placeholder="Escreve uma mensagem..."
                                        required
                                    >{{ old('body') }}</textarea>

                                    @error('body')
                                        <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button
                                    type="submit"
                                    class="inline-flex items-center rounded-xl border border-cyan-300/20 bg-cyan-400/10 px-5 py-3 text-sm font-semibold text-cyan-100 transition hover:bg-cyan-400/20"
                                >
                                    Enviar
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex h-full items-center justify-center p-8">
                            <div class="max-w-md rounded-2xl border border-cyan-300/10 bg-slate-800/70 px-8 py-10 text-center">
                                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-cyan-400/15 text-2xl text-cyan-200">
                                    💬
                                </div>

                                <h3 class="mt-5 text-2xl font-semibold text-white">Bem-vindo ao chat</h3>
                                <p class="mt-3 text-sm leading-6 text-slate-400">
                                    Escolhe uma conversa na barra lateral ou inicia uma nova mensagem direta com um utilizador.
                                </p>
                            </div>
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
