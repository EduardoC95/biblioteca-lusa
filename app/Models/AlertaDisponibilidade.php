<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertaDisponibilidade extends Model
{
    protected $table = 'alertas_disponibilidade';

    protected $fillable = [
        'livro_id',
        'user_id',
        'enviado_em',
    ];

    protected $casts = [
        'enviado_em' => 'datetime',
    ];

    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
