<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl text-cyan-200">Criar Sala</h2>
    </x-slot>

    <div class="p-6">
        <form method="POST" action="{{ route('chat.rooms.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-white">Nome</label>
                <input type="text" name="name" class="w-full p-2 bg-slate-800 text-white rounded" required>
            </div>

            <div class="mb-4">
                <label class="block text-white">Utilizadores</label>
                <select name="users[]" multiple class="w-full p-2 bg-slate-800 text-white rounded">
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <button class="bg-cyan-500 px-4 py-2 rounded text-white">
                Criar Sala
            </button>
        </form>
    </div>
</x-app-layout>
