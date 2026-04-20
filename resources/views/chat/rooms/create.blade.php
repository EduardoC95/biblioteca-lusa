<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl text-cyan-200">Criar Sala</h2>
    </x-slot>

    <div class="p-6">
        <form method="POST" action="{{ route('chat.rooms.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-white mb-2">Nome</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full p-2 bg-slate-800 text-white rounded"
                    required
                >

                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-white mb-2">Utilizadores</label>

                <div class="w-full p-3 bg-slate-800 text-white rounded max-h-60 overflow-y-auto space-y-2">
                    @foreach($users as $user)
                        <label class="flex items-center gap-2 text-white">
                            <input
                                type="checkbox"
                                name="users[]"
                                value="{{ $user->id }}"
                                @checked(collect(old('users'))->contains($user->id))
                                class="accent-cyan-500"
                            >
                            {{ $user->name }}
                        </label>
                    @endforeach
                </div>

                @error('users')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror

                @error('users.*')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-cyan-500 px-4 py-2 rounded text-white">
                Criar Sala
            </button>
        </form>
    </div>
</x-app-layout>
