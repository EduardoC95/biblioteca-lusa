<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Pesquisa de Livros
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <form method="GET" action="{{ route('livros.pesquisa-unificada') }}" class="flex gap-3">
                    <input
                        type="text"
                        name="q"
                        value="{{ $query }}"
                        placeholder="Pesquisar por nome, ISBN ou sinopse..."
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                    >
                    <button
                        type="submit"
                        class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700"
                    >
                        Pesquisar
                    </button>
                </form>
            </div>

            @if ($error)
                <div class="rounded-lg bg-red-100 text-red-800 px-4 py-3">
                    {{ $error }}
                </div>
            @endif

            @if ($query !== '')
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold dark:text-white mb-4">Resultados locais</h3>

                    @if ($livrosLocais->isEmpty())
                        <p class="text-gray-600 dark:text-gray-300">Nenhum livro local encontrado.</p>
                    @else
                        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($livrosLocais as $livro)
                                <div class="border rounded-xl p-4 dark:border-gray-700">
                                    @if ($livro->capa_imagem)
                                        <img
                                            src="{{ $livro->capa_imagem }}"
                                            alt="{{ $livro->nome }}"
                                            class="w-28 h-40 object-cover rounded mb-3"
                                        >
                                    @endif

                                    <h4 class="font-semibold text-lg dark:text-white">{{ $livro->nome }}</h4>

                                    @if ($livro->isbn)
                                        <p class="text-sm text-gray-600 dark:text-gray-300">
                                            <strong>ISBN:</strong> {{ $livro->isbn }}
                                        </p>
                                    @endif

                                    @if ($livro->editora)
                                        <p class="text-sm text-gray-600 dark:text-gray-300">
                                            <strong>Editora:</strong> {{ $livro->editora->nome }}
                                        </p>
                                    @endif

                                    @if ($livro->autores->isNotEmpty())
                                        <p class="text-sm text-gray-600 dark:text-gray-300">
                                            <strong>Autores:</strong> {{ $livro->autores->pluck('nome')->join(', ') }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold dark:text-white mb-4">Resultados Google Books</h3>

                    @if ($livrosGoogle->isEmpty())
                        <p class="text-gray-600 dark:text-gray-300">Nenhum livro externo novo encontrado.</p>
                    @else
                        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($livrosGoogle as $item)
                                <div class="border rounded-xl p-4 dark:border-gray-700">
                                    @if (!empty($item['capa_imagem']))
                                        <img
                                            src="{{ $item['capa_imagem'] }}"
                                            alt="{{ $item['nome'] ?? 'Capa do livro' }}"
                                            class="w-28 h-40 object-cover rounded mb-3"
                                        >
                                    @endif

                                    <h4 class="font-semibold text-lg dark:text-white">
                                        {{ $item['nome'] ?? 'Sem título' }}
                                    </h4>

                                    @if (!empty($item['autores']))
                                        <p class="text-sm text-gray-600 dark:text-gray-300">
                                            <strong>Autores:</strong> {{ $item['autores'] }}
                                        </p>
                                    @endif

                                    @if (!empty($item['editora']))
                                        <p class="text-sm text-gray-600 dark:text-gray-300">
                                            <strong>Editora:</strong> {{ $item['editora'] }}
                                        </p>
                                    @endif

                                    <div class="mt-4">
                                        <form method="POST" action="{{ route('google-books.import', $item['volume_id']) }}">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="px-3 py-2 text-sm rounded bg-green-600 text-white hover:bg-green-700"
                                            >
                                                Importar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
