<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="woodhaven">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Biblioteca Lusa') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=merriweather:400,700,900|nunito:400,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased text-slate-100">
        <x-banner />

        <div class="mx-auto max-w-[1600px] p-4 lg:p-6">
            <div class="grid gap-6 lg:grid-cols-[320px_minmax(0,1fr)]">
                <aside class="neo-panel sage-panel flex min-h-[calc(100vh-3rem)] flex-col gap-6 p-5">
                    @livewire('navigation-menu')
                </aside>

                <main class="min-w-0">
                    @if (isset($header))
                        <div class="neo-panel sage-panel mb-6 rounded-xl p-5">
                            <div class="flex items-center gap-3">
                                <button type="button" onclick="window.history.back()" class="btn btn-outline btn-sm" aria-label="Voltar">
                                    &larr;
                                </button>
                                <div class="min-w-0 flex-1">
                                    {{ $header }}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="alert alert-success mb-6 shadow-md">
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    <div class="neo-panel scanline p-6">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        @stack('modals')
        @livewireScripts
    </body>
</html>





