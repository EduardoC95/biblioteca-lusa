<?php

namespace App\Http\Controllers;

use App\Models\Requisicao;
use App\Models\Review;
use App\Models\User;
use App\Notifications\Admin\NovaReviewPendenteNotification;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ReviewController extends Controller
{
    public function store(Request $request, Requisicao $requisicao): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->role === User::ROLE_CIDADAO, 403);
        abort_unless((int) $requisicao->cidadao_id === (int) $user->id, 403);
        abort_unless($requisicao->estado === Requisicao::ESTADO_DEVOLVIDA, 403, 'Só pode deixar review após a devolução do livro.');

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

        $review->load(['user', 'livro', 'requisicao']);

        $admins = User::query()
            ->where('role', User::ROLE_ADMIN)
            ->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NovaReviewPendenteNotification($review));
        }

        ActivityLogger::log(
            userId: $user->id,
            module: 'reviews',
            objectId: $review->id,
            action: 'create',
            description: 'Review criado para o livro ID ' . $requisicao->livro_id . ' com estado suspenso',
            request: $request
        );

        return back()->with('success', 'Review submetido com sucesso. Ficará visível após aprovação.');
    }
}
