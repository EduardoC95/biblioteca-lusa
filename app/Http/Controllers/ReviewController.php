<?php

namespace App\Http\Controllers;

use App\Models\Requisicao;
use App\Models\Review;
use App\Models\User;
use App\Notifications\Admin\NovaReviewPendenteNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ReviewController extends Controller
{
    public function store(Request $request, Requisicao $requisicao): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->role === 'cidadao', 403);
        abort_unless((int) $requisicao->cidadao_id === (int) $user->id, 403);

        // Ajusta estes campos à tua estrutura real:
        abort_unless(
            in_array($requisicao->estado, ['entregue', 'devolvida', 'finalizada']),
            403,
            'Só pode deixar review após a entrega do livro.'
        );

        if ($requisicao->review()->exists()) {
            return back()->with('error', 'Esta requisição já tem um review associado.');
        }

        $validated = $request->validate([
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'comentario' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $review = Review::create([
            'requisicao_id' => $requisicao->id,
            'livro_id' => $requisicao->livro_id,
            'user_id' => $user->id,
            'rating' => $validated['rating'] ?? null,
            'comentario' => $validated['comentario'],
            'estado' => 'suspenso',
        ]);

        $admins = User::query()->where('role', 'admin')->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NovaReviewPendenteNotification($review));
        }

        return back()->with('success', 'Review submetido com sucesso. Ficará visível após aprovação.');
    }
}
