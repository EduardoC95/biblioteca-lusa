<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use App\Models\Editora;
use App\Models\Livro;
use App\Services\GoogleBooksService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class GoogleBooksImportController extends Controller
{
    public function store(string $volumeId, GoogleBooksService $googleBooks): RedirectResponse
    {
        $volume = $googleBooks->getVolume($volumeId);

        if (!$volume) {
            return back()->with('error', 'Não foi possível obter os dados do livro na Google Books.');
        }

        $mappedBook = $googleBooks->mapBook($volume);
        $volumeInfo = $volume['volumeInfo'] ?? [];

        if (empty($mappedBook['nome'])) {
            return back()->with('error', 'O livro não tem título válido para importação.');
        }

        $existingBook = null;

        if (!empty($mappedBook['isbn'])) {
            $existingBook = Livro::query()
                ->get()
                ->first(function (Livro $livro) use ($mappedBook) {
                    return $livro->isbn === $mappedBook['isbn'];
                });
        }

        if (!$existingBook && !empty($mappedBook['nome'])) {
            $existingBook = Livro::query()
                ->get()
                ->first(function (Livro $livro) use ($mappedBook) {
                    return mb_strtolower($livro->nome) === mb_strtolower($mappedBook['nome']);
                });
        }

        if ($existingBook) {
            return back()->with('info', 'Este livro já existe na base de dados local.');
        }

        DB::transaction(function () use ($mappedBook, $volumeInfo) {
            $editoraId = null;

            $publisherName = trim((string) ($volumeInfo['publisher'] ?? ''));

            if ($publisherName !== '') {
                $editora = Editora::firstOrCreate([
                    'nome' => $publisherName,
                ]);

                $editoraId = $editora->id;
            }

            $livro = Livro::create([
                'isbn' => $mappedBook['isbn'],
                'nome' => $mappedBook['nome'],
                'editora_id' => $editoraId,
                'sinopse' => $mappedBook['sinopse'],
                'capa_imagem' => $mappedBook['capa_imagem'],
                'preco' => $mappedBook['preco'],
                'total_requisicoes' => $mappedBook['total_requisicoes'],
            ]);

            $authorIds = [];

            foreach (($volumeInfo['authors'] ?? []) as $authorName) {
                $authorName = trim((string) $authorName);

                if ($authorName === '') {
                    continue;
                }

                $autor = Autor::firstOrCreate([
                    'nome' => $authorName,
                ]);

                $authorIds[] = $autor->id;
            }

            if (!empty($authorIds)) {
                $livro->autores()->syncWithoutDetaching($authorIds);
            }
        });

        return back()->with('success', 'Livro importado com sucesso para a base de dados local.');
    }
}
