<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Pesquisa Google Books
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <form method="GET" action="{{ route('google-books.search') }}" class="flex gap-3">
                    <input
                        type="text"
                        name="q"
                        value="{{ $query }}"
                        placeholder="Pesquisar livros..."
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

            @if (session('success'))
                <div class="rounded-lg bg-green-100 text-green-800 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('info'))
                <div class="rounded-lg bg-blue-100 text-blue-800 px-4 py-3">
                    {{ session('info') }}
                </div>
            @endif

            @if ($error)
                <div class="rounded-lg bg-red-100 text-red-800 px-4 py-3">
                    {{ $error }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg bg-red-100 text-red-800 px-4 py-3">
                    {{ session('error') }}
                </div>
            @endif

            @if ($query !== '')
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                        Resultados para: <strong>{{ $query }}</strong>
                    </p>

                    @if (empty($items))
                        <p class="text-gray-600 dark:text-gray-300">
                            Nenhum livro encontrado.
                        </p>
                    @else
                        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($items as $item)
                                <div class="border rounded-xl p-4 dark:border-gray-700">
                                    @if (!empty($item['capa_imagem']))
                                        <img
                                            src="{{ $item['capa_imagem'] }}"
                                            alt="{{ $item['nome'] ?? 'Capa do livro' }}"
                                            class="w-28 h-40 object-cover rounded mb-3"
                                        >
                                    @endif

                                    <h3 class="font-semibold text-lg dark:text-white">
                                        {{ $item['nome'] ?? 'Sem título' }}
                                    </h3>

                                    @if (!empty($item['autores']))
                                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                            <strong>Autores:</strong> {{ $item['autores'] }}
                                        </p>
                                    @endif

                                    @if (!empty($item['editora']))
                                        <p class="text-sm text-gray-600 dark:text-gray-300">
                                            <strong>Editora:</strong> {{ $item['editora'] }}
                                        </p>
                                    @endif

                                    @if (!empty($item['data_publicacao']))
                                        <p class="text-sm text-gray-600 dark:text-gray-300">
                                            <strong>Publicação:</strong> {{ $item['data_publicacao'] }}
                                        </p>
                                    @endif

                                    @if (!empty($item['sinopse']))
                                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-3">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($item['sinopse']), 180) }}
                                        </p>
                                    @endif

                                    <div class="mt-4">
                                        @if (!empty($item['volume_id']))
                                            <form method="POST" action="{{ route('google-books.import', $item['volume_id']) }}">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="px-3 py-2 text-sm rounded bg-green-600 text-white hover:bg-green-700"
                                                >
                                                    Importar
                                                </button>
                                            </form>
                                        @else
                                            <button
                                                type="button"
                                                class="px-3 py-2 text-sm rounded bg-green-600 text-white opacity-60 cursor-not-allowed"
                                                disabled
                                            >
                                                Importar
                                            </button>
                                        @endif
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
