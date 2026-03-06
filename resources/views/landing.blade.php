<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="woodhaven">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Biblioteca Lusa</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=merriweather:400,700,900|nunito:400,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen font-sans text-slate-100">
        <div class="mx-auto flex min-h-screen max-w-6xl items-center justify-center px-4 py-10">
            <section class="neo-panel neon-border w-full max-w-3xl p-8 text-center md:p-12">
                <p class="font-display text-sm uppercase tracking-[0.35em] text-cyan-300">Sistema Online</p>
                <h1 class="mt-5 font-display text-6xl text-cyan-200 md:text-7xl">Biblioteca Lusa</h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg text-slate-300">A sua biblioteca online</p>

                <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('register') }}" class="btn btn-primary">Iniciar Interface</a>
                    <a href="{{ route('login') }}" class="btn btn-outline">Login Direto</a>
                    <a href="{{ route('catalogo.index') }}" class="btn btn-secondary">Ver Catálogo</a>
                </div>
            </section>
        </div>
    </body>
</html>



