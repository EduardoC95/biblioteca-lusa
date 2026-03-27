<?php

namespace App\Services;

use App\Models\Livro;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class LivrosRelacionadosService
{
    protected array $stopwords = [
        'a','à','ao','aos','as','às','o','os',
        'de','da','das','do','dos',
        'e','é','em','na','nas','no','nos',
        'um','uma','uns','umas',
        'por','para','com','sem','sob','sobre',
        'que','se','ou','como','mais','menos',
        'muito','muita','muitos','muitas',
        'já','também','ser','estar','foi','era','são',
        'tem','têm','há','não','sim',
        'lhe','lhes','ele','ela','eles','elas'
    ];

    public function relacionadosPara(Livro $livro, int $limit = 4): Collection
    {
        if (blank($livro->sinopse)) {
            return collect();
        }

        $livros = Livro::query()
            ->with('editora')
            ->where('id', '!=', $livro->id)
            ->whereNotNull('sinopse')
            ->where('sinopse', '!=', '')
            ->get();

        if ($livros->isEmpty()) {
            return collect();
        }

        $tokensLivroAtual = $this->tokenize(
            trim(($livro->nome ?? '') . ' ' . ($livro->sinopse ?? ''))
        );

        if (empty($tokensLivroAtual)) {
            return collect();
        }

        $scores = [];

        foreach ($livros as $outro) {
            $tokensOutro = $this->tokenize(
                trim(($outro->nome ?? '') . ' ' . ($outro->sinopse ?? ''))
            );

            if (empty($tokensOutro)) {
                continue;
            }

            $common = array_intersect(
                array_keys($tokensLivroAtual),
                array_keys($tokensOutro)
            );

            $score = count($common);

            if ($score > 0) {
                $scores[$outro->id] = $score;
            }
        }

        arsort($scores);

        $ids = collect($scores)
            ->keys()
            ->take($limit)
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        $livrosRelacionados = $livros->whereIn('id', $ids);

        return $ids
            ->map(fn ($id) => $livrosRelacionados->firstWhere('id', $id))
            ->filter()
            ->values();
    }

    protected function tokenize(string $text): array
    {
        $text = Str::lower($text);
        $text = Str::ascii($text);
        $text = preg_replace('/[^\pL\s]/u', ' ', $text);
        $text = preg_replace('/\s+/', ' ', trim($text));

        $words = explode(' ', $text);

        $tokens = [];

        foreach ($words as $word) {
            $word = trim($word);

            if ($word === '') {
                continue;
            }

            if (mb_strlen($word) < 3) {
                continue;
            }

            if (in_array($word, $this->stopwords, true)) {
                continue;
            }

            $tokens[$word] = true;
        }

        return $tokens;
    }
}
