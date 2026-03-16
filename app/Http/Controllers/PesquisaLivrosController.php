<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Services\GoogleBooksService;
use Illuminate\Http\Request;

class PesquisaLivrosController extends Controller
{
    public function index(Request $request, GoogleBooksService $googleBooks)
    {
        $query = trim((string) $request->get('q', ''));
        $queryNormalizada = mb_strtolower($query);

        $livrosLocais = collect();
        $livrosGoogle = collect();
        $error = null;

        if ($query !== '') {
            $livrosLocais = Livro::query()
                ->with(['editora', 'autores'])
                ->get()
                ->filter(function (Livro $livro) use ($queryNormalizada) {
                    $nome = mb_strtolower((string) $livro->nome);
                    $isbn = mb_strtolower((string) $livro->isbn);
                    $sinopse = mb_strtolower((string) $livro->sinopse);

                    return str_contains($nome, $queryNormalizada)
                        || str_contains($isbn, $queryNormalizada)
                        || str_contains($sinopse, $queryNormalizada);
                })
                ->values();

            try {
                $livrosGoogle = collect($googleBooks->search($query, 12))
                    ->map(fn (array $volume) => $googleBooks->mapBook($volume))
                    ->filter(function (array $item) use ($livrosLocais) {
                        return ! $livrosLocais->contains(function (Livro $livro) use ($item) {
                            $mesmoIsbn = ! empty($item['isbn']) && $livro->isbn === $item['isbn'];
                            $mesmoNome = ! empty($item['nome'])
                                && mb_strtolower((string) $livro->nome) === mb_strtolower((string) $item['nome']);

                            return $mesmoIsbn || $mesmoNome;
                        });
                    })
                    ->values();
            } catch (\Throwable $e) {
                $error = 'Não foi possível pesquisar livros externos na Google Books API.';
            }
        }

        return view('livros.pesquisa-unificada', [
            'query' => $query,
            'livrosLocais' => $livrosLocais,
            'livrosGoogle' => $livrosGoogle,
            'error' => $error,
        ]);
    }
}
