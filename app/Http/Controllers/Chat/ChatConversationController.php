<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatConversationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $conversations = ChatConversation::query()
            ->with([
                'room',
                'users',
                'latestMessage.user',
            ])
            ->whereHas('users', fn ($query) => $query->where('users.id', $user->id))
            ->latest('updated_at')
            ->get();

        $activeConversation = $conversations->first();

        if ($activeConversation) {
            $activeConversation->load([
                'room',
                'users',
                'messagesOldestFirst.user',
            ]);
        }

        $teamMembers = User::query()
            ->where('id', '!=', $user->id)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', User::STATUS_ATIVO);
            })
            ->orderBy('name')
            ->get();

        return view('chat.index', compact('conversations', 'activeConversation', 'teamMembers'));
    }

    public function show(Request $request, ChatConversation $conversation): View
    {
        $user = $request->user();

        abort_unless(
            $conversation->users()->where('users.id', $user->id)->exists(),
            403
        );

        $conversations = ChatConversation::query()
            ->with([
                'room',
                'users',
                'latestMessage.user',
            ])
            ->whereHas('users', fn ($query) => $query->where('users.id', $user->id))
            ->latest('updated_at')
            ->get();

        $activeConversation = $conversation->load([
            'room',
            'users',
            'messagesOldestFirst.user',
        ]);

        $teamMembers = User::query()
            ->where('id', '!=', $user->id)
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', User::STATUS_ATIVO);
            })
            ->orderBy('name')
            ->get();

        return view('chat.index', compact('conversations', 'activeConversation', 'teamMembers'));
    }

    public function startDirect(Request $request, User $user): RedirectResponse
{
    $authUser = $request->user();

    abort_if($authUser->id === $user->id, 403);

    $existingConversation = ChatConversation::query()
        ->where('type', 'direct')
        ->whereHas('users', fn ($query) => $query->where('users.id', $authUser->id))
        ->whereHas('users', fn ($query) => $query->where('users.id', $user->id))
        ->withCount('users')
        ->get()
        ->first(fn ($conversation) => (int) $conversation->users_count === 2);

    if ($existingConversation) {
        // garante que os dois estão associados
        $existingConversation->users()->syncWithoutDetaching([
            $authUser->id,
            $user->id,
        ]);

        return redirect()->route('chat.show', $existingConversation);
    }

    $conversation = ChatConversation::create([
        'type' => 'direct',
    ]);

    $conversation->users()->syncWithoutDetaching([
        $authUser->id,
        $user->id,
    ]);

    return redirect()->route('chat.show', $conversation);
}
}
