<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\RedirectResponse;

class ChatMessageController extends Controller
{
    public function store(StoreMessageRequest $request, ChatConversation $conversation): RedirectResponse
{
    $user = $request->user();

    $conversation->loadMissing('users');

    $isParticipant = $conversation->users
        ->pluck('id')
        ->contains($user->id);

    if (! $isParticipant) {
        return redirect()
            ->route('chat.index')
            ->with('error', 'Não tens permissão para enviar mensagens nesta conversa.');
    }

    ChatMessage::create([
        'chat_conversation_id' => $conversation->id,
        'user_id' => $user->id,
        'body' => trim((string) $request->validated('body')),
    ]);

    $conversation->touch();

    return redirect()
        ->route('chat.show', $conversation)
        ->with('success', 'Mensagem enviada com sucesso.');
}
}
