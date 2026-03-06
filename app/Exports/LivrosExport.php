<?php

namespace App\Exports;

use App\Models\Livro;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LivrosExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Livro::with(['editora', 'autores'])
            ->get()
            ->map(function (Livro $livro) {
                return [
                    'isbn' => $livro->isbn,
                    'nome' => $livro->nome,
                    'editora' => $livro->editora?->nome,
                    'autores' => $livro->autores->pluck('nome')->implode(', '),
                    'sinopse' => $livro->sinopse,
                    'preco' => $livro->preco,
                    'criado_em' => $livro->created_at?->format('Y-m-d H:i:s'),
                ];
            });
    }

    public function headings(): array
    {
        return ['ISBN', 'Nome', 'Editora', 'Autores', 'Sinopse', 'Preco', 'Criado em'];
    }
}
