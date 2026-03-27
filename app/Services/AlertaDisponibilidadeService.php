<?php

namespace App\Services;

use App\Models\AlertaDisponibilidade;
use App\Models\Livro;
use App\Notifications\LivroDisponivelNotification;

class AlertaDisponibilidadeService
{
    public function criarAlerta(Livro $livro, int $userId): void
    {
        AlertaDisponibilidade::firstOrCreate([
            'livro_id' => $livro->id,
            'user_id' => $userId,
        ]);
    }

    public function utilizadorJaTemAlertaPendente(Livro $livro, int $userId): bool
    {
        return AlertaDisponibilidade::query()
            ->where('livro_id', $livro->id)
            ->where('user_id', $userId)
            ->whereNull('enviado_em')
            ->exists();
    }

    public function enviarAlertasSeDisponivel(Livro $livro): void
    {
        if (! $livro->estaDisponivelParaRequisicao()) {
            return;
        }

        $alertas = AlertaDisponibilidade::query()
            ->with('user')
            ->where('livro_id', $livro->id)
            ->whereNull('enviado_em')
            ->get();

        foreach ($alertas as $alerta) {
            if ($alerta->user) {
                $alerta->user->notify(new LivroDisponivelNotification($livro));
            }

            $alerta->update([
                'enviado_em' => now(),
            ]);
        }
    }
}
