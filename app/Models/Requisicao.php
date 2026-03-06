<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Requisicao extends Model
{
    protected $table = 'requisicoes';

    protected $fillable = [
        'numero_sequencial',
        'livro_id',
        'cidadao_id',
        'cidadao_nome',
        'cidadao_email',
        'cidadao_foto_path',
        'data_requisicao',
        'data_prevista_entrega',
        'data_real_entrega',
        'dias_decorridos',
        'devolucao_confirmada_por_admin_id',
        'reminder_enviado_em',
    ];

    protected function casts(): array
    {
        return [
            'cidadao_nome' => 'encrypted',
            'cidadao_email' => 'encrypted',
            'cidadao_foto_path' => 'encrypted',
            'data_requisicao' => 'date',
            'data_prevista_entrega' => 'date',
            'data_real_entrega' => 'date',
            'dias_decorridos' => 'integer',
            'reminder_enviado_em' => 'datetime',
        ];
    }

    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }

    public function cidadao(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cidadao_id');
    }

    public function devolucaoConfirmadaPorAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'devolucao_confirmada_por_admin_id');
    }

    public function scopeAtivas(Builder $query): Builder
    {
        return $query->whereNull('data_real_entrega');
    }

    public function scopeEntregues(Builder $query): Builder
    {
        return $query->whereNotNull('data_real_entrega');
    }

    public function getEstaAtivaAttribute(): bool
    {
        return $this->data_real_entrega === null;
    }

    public function getDiasEmAbertoAttribute(): int
    {
        $inicio = CarbonImmutable::instance($this->data_requisicao);
        $fim = $this->data_real_entrega
            ? CarbonImmutable::instance($this->data_real_entrega)
            : CarbonImmutable::today();

        return max(0, $inicio->diffInDays($fim));
    }
}
