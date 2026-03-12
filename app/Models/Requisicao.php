<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Requisicao extends Model
{
    protected $table = 'requisicoes';

    public const ESTADO_PENDENTE_ENTREGA = 'pendente_entrega';
    public const ESTADO_ATIVA = 'ativa';
    public const ESTADO_DEVOLVIDA = 'devolvida';

    protected $fillable = [
        'numero_sequencial',
        'estado',
        'livro_id',
        'cidadao_id',
        'cidadao_nome',
        'cidadao_email',
        'cidadao_foto_path',
        'data_requisicao',
        'data_entrega_prevista',
        'data_entrega_real',
        'data_devolucao_prevista',
        'data_devolucao_real',
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
            'data_entrega_prevista' => 'date',
            'data_entrega_real' => 'date',
            'data_devolucao_prevista' => 'date',
            'data_devolucao_real' => 'date',
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
        return $query->whereIn('estado', [
            self::ESTADO_PENDENTE_ENTREGA,
            self::ESTADO_ATIVA,
        ]);
    }

    public function scopePendentesEntrega(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_PENDENTE_ENTREGA);
    }

    public function scopeEmPosseDoCidadao(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_ATIVA);
    }

    public function scopeDevolvidas(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_DEVOLVIDA);
    }

    public function scopeEntregues(Builder $query): Builder
    {
        return $query->whereNotNull('data_entrega_real');
    }

    public function getEstaAtivaAttribute(): bool
    {
        return in_array($this->estado, [
            self::ESTADO_PENDENTE_ENTREGA,
            self::ESTADO_ATIVA,
        ], true);
    }

    public function getEstaPendenteEntregaAttribute(): bool
    {
        return $this->estado === self::ESTADO_PENDENTE_ENTREGA;
    }

    public function getEstaEmPosseDoCidadaoAttribute(): bool
    {
        return $this->estado === self::ESTADO_ATIVA;
    }

    public function getEstaDevolvidaAttribute(): bool
    {
        return $this->estado === self::ESTADO_DEVOLVIDA;
    }

    public function getDiasEmAbertoAttribute(): int
    {
        $inicio = $this->data_entrega_real
            ? CarbonImmutable::instance($this->data_entrega_real)
            : CarbonImmutable::instance($this->data_requisicao);

        $fim = $this->data_devolucao_real
            ? CarbonImmutable::instance($this->data_devolucao_real)
            : CarbonImmutable::today();

        return max(0, $inicio->diffInDays($fim));
    }
}
