<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-3xl text-cyan-200">Editar Livro</h2>
    </x-slot>

    <form method="POST" action="{{ route('livros.update', $livro) }}" enctype="multipart/form-data" class="space-y-4 rounded-xl border border-cyan-300/20 bg-slate-900/70 p-6">
        @csrf
        @method('PUT')

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="label"><span class="label-text">ISBN</span></label>
                <input type="text" name="isbn" value="{{ old('isbn', $livro->isbn) }}" class="input input-bordered w-full" required />
                @error('isbn') <p class="text-error text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label"><span class="label-text">Nome</span></label>
                <input type="text" name="nome" value="{{ old('nome', $livro->nome) }}" class="input input-bordered w-full" required />
                @error('nome') <p class="text-error text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label"><span class="label-text">Editora</span></label>
                <select name="editora_id" class="select select-bordered w-full" required>
                    <option value="">Selecionar</option>
                    @foreach ($editoras as $editora)
                        <option value="{{ $editora->id }}" @selected((int) old('editora_id', $livro->editora_id) === $editora->id)>{{ $editora->nome }}</option>
                    @endforeach
                </select>
                @error('editora_id') <p class="text-error text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label"><span class="label-text">Pre&ccedil;o</span></label>
                <input type="number" step="0.01" min="0" name="preco" value="{{ old('preco', (float) $livro->preco) }}" class="input input-bordered w-full" required />
                @error('preco') <p class="text-error text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="label"><span class="label-text">Autores (m&uacute;ltiplos)</span></label>
            @php
                $selectedAuthors = old('autores', $livro->autores->pluck('id')->all());
            @endphp
            <select name="autores[]" multiple class="select select-bordered w-full h-40" required>
                @foreach ($autores as $autor)
                    <option value="{{ $autor->id }}" @selected(in_array($autor->id, $selectedAuthors))>{{ $autor->nome }}</option>
                @endforeach
            </select>
            @error('autores') <p class="text-error text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label"><span class="label-text">Sinopse</span></label>
            <textarea name="sinopse" class="textarea textarea-bordered w-full" rows="6">{{ old('sinopse', $livro->sinopse) }}</textarea>
        </div>

        <div>
            <label class="label"><span class="label-text">Imagem da capa</span></label>
            <input type="file" name="capa_imagem" class="file-input file-input-bordered w-full" accept="image/*" />
            @if ($livro->capa_imagem)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($livro->capa_imagem) }}" class="mt-2 h-24 rounded" alt="Capa" />
            @endif
        </div>

        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary h-10 min-h-10 px-4">Atualizar</button>
            <a href="{{ route('livros.index') }}" class="btn btn-outline h-10 min-h-10 px-4">Cancelar</a>
        </div>
    </form>
</x-app-layout>





