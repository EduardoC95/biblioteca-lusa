<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleBooksService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.google_books.base_url');
        $this->apiKey = config('services.google_books.key');
    }

    /**
     * Pesquisar livros na Google Books API
     */
    public function search(string $query, int $maxResults = 10): array
    {
        $response = Http::get($this->baseUrl . '/volumes', [
            'q' => $query,
            'maxResults' => $maxResults,
            'key' => $this->apiKey,
        ]);

        if (!$response->successful()) {
            return [];
        }

        return $response->json()['items'] ?? [];
    }

    /**
     * Obter um livro específico pelo volumeId
     */
    public function getVolume(string $volumeId): ?array
    {
        $response = Http::get($this->baseUrl . '/volumes/' . $volumeId, [
            'key' => $this->apiKey,
        ]);

        if (!$response->successful()) {
            return null;
        }

        return $response->json();
    }

    /**
     * Mapear dados da Google para estrutura da nossa BD
     */
    public function mapBook(array $volume): array
    {
        $info = $volume['volumeInfo'] ?? [];

        return [

            'google_books_id' => $volume['id'] ?? null,

            'titulo' => $info['title'] ?? null,

            'autores' => isset($info['authors'])
                ? implode(', ', $info['authors'])
                : null,

            'editora' => $info['publisher'] ?? null,

            'data_publicacao' => $info['publishedDate'] ?? null,

            'descricao' => $info['description'] ?? null,

            'paginas' => $info['pageCount'] ?? null,

            'idioma' => $info['language'] ?? null,

            'capa_url' => $info['imageLinks']['thumbnail'] ?? null,

        ];
    }
}
