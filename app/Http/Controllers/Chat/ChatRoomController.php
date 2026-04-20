<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChatRoomRequest;
use App\Models\ChatRoom;
use App\Models\ChatConversation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ChatRoomController extends Controller
{
    public function create(): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $users = User::query()
            ->where('id', '!=', auth()->id())
            ->where('status', User::STATUS_ATIVO)
            ->orderBy('name')
            ->get();

        return view('chat.rooms.create', compact('users'));
    }

    public function store(StoreChatRoomRequest $request): RedirectResponse
    {
        $admin = $request->user();

        abort_unless($admin->isAdmin(), 403);

        $data = $request->validated();

        $room = ChatRoom::create([
            'name' => $data['name'],
            'avatar' => $data['avatar'] ?? null,
            'reference' => $data['reference'] ?? Str::slug($data['name']) . '-' . Str::lower(Str::random(6)),
            'created_by' => $admin->id,
        ]);

        // criar conversa associada
        $conversation = ChatConversation::create([
            'type' => 'room',
            'chat_room_id' => $room->id,
        ]);

        // adicionar utilizadores à conversa
        $users = collect($data['users'] ?? [])
            ->push($admin->id)
            ->unique()
            ->toArray();

        $conversation->users()->syncWithoutDetaching($users);

        return redirect()
            ->route('chat.show', $conversation)
            ->with('success', 'Sala criada com sucesso.');
    }
}
