<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function requisicoes(): HasMany
    {
        return $this->hasMany(Requisicao::class);
    }

    public function requisicaoAtiva(): HasOne
    {
        return $this->hasOne(Requisicao::class)->whereNull('data_real_entrega');
    }

    public function estaDisponivelParaRequisicao(): bool
    {
        return ! $this->requisicoes()->whereNull('data_real_entrega')->exists();
    }
}
