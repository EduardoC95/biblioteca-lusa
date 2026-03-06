<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-3xl text-cyan-200">Editar Autor</h2>
    </x-slot>

    <form method="POST" action="{{ route('autores.update', $autor) }}" enctype="multipart/form-data" class="space-y-4 rounded-xl border border-cyan-300/20 bg-slate-900/70 p-6">
        @csrf
        @method('PUT')

        <div>
            <label class="label"><span class="label-text">Nome</span></label>
            <input type="text" name="nome" value="{{ old('nome', $autor->nome) }}" class="input input-bordered w-full" required />
            @error('nome') <p class="text-error text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label"><span class="label-text">Foto</span></label>
            <input type="file" name="foto" class="file-input file-input-bordered w-full" accept="image/*" />
            @if ($autor->foto)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($autor->foto) }}" class="mt-2 h-24 rounded" alt="Foto" />
            @endif
            @error('foto') <p class="text-error text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label"><span class="label-text">Bibliografia</span></label>
            <textarea name="bibliografia" class="textarea textarea-bordered w-full" rows="8">{{ old('bibliografia', $autor->bibliografia) }}</textarea>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary h-10 min-h-10 px-4">Atualizar</button>
            <a href="{{ route('autores.index') }}" class="btn btn-outline h-10 min-h-10 px-4">Cancelar</a>
        </div>
    </form>
</x-app-layout>



