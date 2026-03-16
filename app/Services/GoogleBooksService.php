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

    public function search(string $query, int $maxResults = 10): array
    {
        $response = Http::get($this->baseUrl . '/volumes', [
            'q' => $query,
            'maxResults' => $maxResults,
            'key' => $this->apiKey,
        ]);

        if (! $response->successful()) {
            return [];
        }

        return $response->json()['items'] ?? [];
    }

    public function getVolume(string $volumeId): ?array
    {
        $response = Http::get($this->baseUrl . '/volumes/' . $volumeId, [
            'key' => $this->apiKey,
        ]);

        if (! $response->successful()) {
            return null;
        }

        return $response->json();
    }

    public function mapBook(array $volume): array
    {
        $info = $volume['volumeInfo'] ?? [];

        $isbn = null;
        $autores = null;
        $editora = $info['publisher'] ?? null;
        $dataPublicacao = $info['publishedDate'] ?? null;

        if (! empty($info['industryIdentifiers'])) {
            foreach ($info['industryIdentifiers'] as $identifier) {
                if (
                    ($identifier['type'] ?? null) === 'ISBN_13' ||
                    ($identifier['type'] ?? null) === 'ISBN_10'
                ) {
                    $isbn = $identifier['identifier'] ?? null;
                    break;
                }
            }
        }

        if (! empty($info['authors'])) {
            $autores = implode(', ', $info['authors']);
        }

        return [
            'volume_id' => $volume['id'] ?? null,
            'isbn' => $isbn,
            'nome' => $info['title'] ?? null,
            'autores' => $autores,
            'editora' => $editora,
            'data_publicacao' => $dataPublicacao,
            'sinopse' => $info['description'] ?? null,
            'capa_imagem' => $info['imageLinks']['thumbnail'] ?? null,
            'preco' => null,
            'total_requisicoes' => 0,
        ];
    }
}
