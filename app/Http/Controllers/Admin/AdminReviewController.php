<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Notifications\Cidadao\ReviewModeradaNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminReviewController extends Controller
{
    public function index(Request $request): View
    {
        $estado = $request->get('estado');

        $reviews = Review::query()
            ->with(['user', 'livro', 'requisicao'])
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews', 'estado'));
    }

    public function show(Review $review): View
    {
        $review->load(['user', 'livro', 'requisicao', 'moderador']);

        return view('admin.reviews.show', compact('review'));
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $validated = $request->validate([
            'estado' => ['required', 'in:ativo,recusado'],
            'justificacao_recusa' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($validated['estado'] === 'recusado' && blank($validated['justificacao_recusa'] ?? null)) {
            return back()
                ->withErrors(['justificacao_recusa' => 'A justificação é obrigatória quando a review é recusada.'])
                ->withInput();
        }

        $review->update([
            'estado' => $validated['estado'],
            'justificacao_recusa' => $validated['estado'] === 'recusado'
                ? $validated['justificacao_recusa']
                : null,
            'moderado_em' => now(),
            'moderado_por' => $request->user()->id,
        ]);

        $review->user->notify(new ReviewModeradaNotification($review));

        return redirect()
            ->route('admin.reviews.show', $review)
            ->with('success', 'Estado da review atualizado com sucesso.');
    }
}
