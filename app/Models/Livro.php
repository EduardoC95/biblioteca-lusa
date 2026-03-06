<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Livro extends Model
{
    protected $fillable = [
        'isbn',
        'nome',
        'editora_id',
        'sinopse',
        'capa_imagem',
        'preco',
        'total_requisicoes',
    ];

    protected function casts(): array
    {
        return [
            'isbn' => 'encrypted',
            'nome' => 'encrypted',
            'sinopse' => 'encrypted',
            'capa_imagem' => 'encrypted',
            'preco' => 'encrypted',
            'total_requisicoes' => 'integer',
        ];
    }

    public function editora(): BelongsTo
    {
        return $this->belongsTo(Editora::class);
    }

    public function autores(): BelongsToMany
    {
        return $this->belongsToMany(Autor::class, 'autor_livro');
    }
}
