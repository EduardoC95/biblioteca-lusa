<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    public const ESTADO_SUSPENSO = 'suspenso';
    public const ESTADO_ATIVO = 'ativo';
    public const ESTADO_RECUSADO = 'recusado';

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

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'moderado_em' => 'datetime',
        ];
    }

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
