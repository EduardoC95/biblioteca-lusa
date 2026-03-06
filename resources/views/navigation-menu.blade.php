<div class="flex h-full flex-col" x-data="{ profileOpen: false }">
    <a href="{{ route('dashboard') }}" class="rounded-xl border border-cyan-300/25 bg-slate-950/70 px-4 py-5 font-display text-3xl tracking-wide text-cyan-300">
        Biblioteca Lusa
    </a>

    <nav class="rounded-xl border border-cyan-300/20 bg-slate-900/70 p-3">
        <ul class="menu gap-1">
            <li><a href="{{ route('catalogo.index') }}" class="{{ request()->routeIs('catalogo.*') ? 'active' : '' }}">Catálogo</a></li>
            <li><a href="{{ route('requisicoes.index') }}" class="{{ request()->routeIs('requisicoes.*') ? 'active' : '' }}">Requisições</a></li>
            <li><a href="{{ route('livros.index') }}" class="{{ request()->routeIs('livros.*') ? 'active' : '' }}">Livros</a></li>
            <li><a href="{{ route('autores.index') }}" class="{{ request()->routeIs('autores.*') ? 'active' : '' }}">Autores</a></li>
            <li><a href="{{ route('editoras.index') }}" class="{{ request()->routeIs('editoras.*') ? 'active' : '' }}">Editoras</a></li>
            @if (Auth::user()->isAdmin())
                <li><a href="{{ route('cidadaos.index') }}" class="{{ request()->routeIs('cidadaos.*') ? 'active' : '' }}">Cidadãos</a></li>
            @endif
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
        <button type="button" @click="profileOpen = !profileOpen" class="btn btn-outline btn-primary w-full justify-between">
            <span>{{ Auth::user()->name }}</span>
            <span>{{ Auth::user()->isAdmin() ? 'Admin' : 'Cidadão' }}</span>
        </button>

        <div x-show="profileOpen" x-transition class="mt-2 space-y-2" x-cloak>
            <a href="{{ route('profile.show') }}" class="btn btn-outline w-full">Perfil</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-error w-full">Terminar sessão</button>
            </form>
        </div>
    </div>
</div>

