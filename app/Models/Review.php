<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'requisicao_id',
        'livro_id',
        'user_id',
        'rating',
        'comentario',
        'estado',
        'justificacao_recusa',
        'moderado_em',
        'moderado_por',
    ];

    public function requisicao(): BelongsTo
    {
        return $this->belongsTo(Requisicao::class);
    }

    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moderador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderado_por');
    }
}
