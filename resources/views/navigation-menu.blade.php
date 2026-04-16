<div class="flex h-full flex-col" x-data="{ profileOpen: false }">
    <a href="{{ auth()->check() ? route('dashboard') : route('landing') }}" class="rounded-xl border border-cyan-300/25 bg-slate-950/70 px-4 py-5 font-display text-3xl tracking-wide text-cyan-300">
        Biblioteca Lusa
    </a>

    <nav class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-3">
        <ul class="menu gap-1">
            <li>
                <a href="{{ route('catalogo.index') }}" class="{{ request()->routeIs('catalogo.*') ? 'active' : '' }}">
                    Cat&aacute;logo
                </a>
            </li>

            @auth
                <li>
                    <a href="{{ route('chat.index') }}" class="{{ request()->routeIs('chat.*') ? 'active' : '' }}">
                        Chat
                    </a>
                </li>

                <li>
                    <a href="{{ route('requisicoes.index') }}" class="{{ request()->routeIs('requisicoes.*') ? 'active' : '' }}">
                        Requisi&ccedil;&otilde;es
                    </a>
                </li>

                <li>
                    <a href="{{ route('livros.index') }}" class="{{ request()->routeIs('livros.*') ? 'active' : '' }}">
                        Livros
                    </a>
                </li>

                <li>
                    <a href="{{ route('autores.index') }}" class="{{ request()->routeIs('autores.*') ? 'active' : '' }}">
                        Autores
                    </a>
                </li>

                <li>
                    <a href="{{ route('editoras.index') }}" class="{{ request()->routeIs('editoras.*') ? 'active' : '' }}">
                        Editoras
                    </a>
                </li>

                <li>
                    <a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.*') ? 'active' : '' }}">
                        Carrinho
                    </a>
                </li>

                @if (auth()->user()->isAdmin())
                    <li>
                        <a href="{{ route('cidadaos.index') }}" class="{{ request()->routeIs('cidadaos.*') ? 'active' : '' }}">
                            Cidad&atilde;os
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                            Reviews
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                            Compras
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.logs.index') }}" class="{{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
                            Logs
                        </a>
                    </li>
                @endif
            @endauth
        </ul>
    </nav>

    @php
        $sidebarBooksImagePath = public_path('images/sidebar-books.jpg');
        $sidebarBooksImage = asset('images/sidebar-books.jpg');
        $sidebarBooksImageVersion = file_exists($sidebarBooksImagePath) ? ('?v=' . filemtime($sidebarBooksImagePath)) : '';
    @endphp

    <div class="flex min-h-[280px] flex-1 overflow-hidden rounded-xl border border-cyan-300/20 bg-slate-900/70 p-3">
        @if (file_exists($sidebarBooksImagePath))
            <img src="{{ $sidebarBooksImage . $sidebarBooksImageVersion }}" alt="Livros" class="h-full w-full rounded-lg object-cover object-center" />
        @else
            <div class="flex h-full items-center justify-center p-4 text-center text-sm text-slate-500">
                Imagem da biblioteca
            </div>
        @endif
    </div>

    <div class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-3">
        @auth
            <button type="button" @click="profileOpen = !profileOpen" class="btn btn-outline btn-primary w-full justify-between">
                <span>{{ auth()->user()->name }}</span>
                <span>{{ auth()->user()->isAdmin() ? 'Admin' : 'Cidadão' }}</span>
            </button>

            <div x-show="profileOpen" x-transition class="mt-2 space-y-2" x-cloak>
                <a href="{{ route('profile.show') }}" class="btn btn-outline w-full">Perfil</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-error w-full">Terminar sess&atilde;o</button>
                </form>
            </div>
        @else
            <div class="space-y-2">
                <div class="rounded-md border border-cyan-300/20 bg-slate-950/50 px-3 py-2 text-center text-sm text-slate-300">
                    Visitante
                </div>

                <a href="{{ route('login') }}" class="btn btn-outline w-full">Iniciar sess&atilde;o</a>
                <a href="{{ route('register') }}" class="btn btn-primary w-full">Registar</a>
            </div>
        @endauth
    </div>
</div>
