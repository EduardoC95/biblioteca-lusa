<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Services\AlertaDisponibilidadeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AlertaDisponibilidadeController extends Controller
{
    public function store(
        Request $request,
        Livro $livro,
        AlertaDisponibilidadeService $alertaDisponibilidadeService
    ): RedirectResponse {
        $user = $request->user();

        if (! $user || ! $user->isCidadao()) {
            return back()->with('status', 'Apenas cidadãos podem criar alertas de disponibilidade.');
        }

        if ($livro->estaDisponivelParaRequisicao()) {
            return back()->with('status', 'Este livro já se encontra disponível para requisição.');
        }

        if ($alertaDisponibilidadeService->utilizadorJaTemAlertaPendente($livro, $user->id)) {
            return back()->with('status', 'Já tem um alerta pendente para este livro.');
        }

        $alertaDisponibilidadeService->criarAlerta($livro, $user->id);

        return back()->with('status', 'Receberá um email quando este livro ficar disponível.');
    }
}
