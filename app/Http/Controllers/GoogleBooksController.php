<?php

namespace App\Http\Controllers;

use App\Services\GoogleBooksService;
use Illuminate\Http\Request;

class GoogleBooksController extends Controller
{
    public function search(Request $request, GoogleBooksService $googleBooks)
    {
        $query = trim((string) $request->get('q', ''));

        $items = [];
        $error = null;

        if ($query !== '') {
            try {
                $items = collect($googleBooks->search($query, 12))
                    ->map(fn(array $volume) => $googleBooks->mapBook($volume))
                    ->all();
            } catch (\Throwable $e) {
                $error = 'Não foi possível pesquisar livros na Google Books API.';
            }
        }

        return view('google-books.search', [
            'query' => $query,
            'items' => $items,
            'error' => $error,
        ]);
    }
}
