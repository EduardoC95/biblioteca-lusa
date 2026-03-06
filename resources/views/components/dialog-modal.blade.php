@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="px-6 py-4">
        <div class="text-lg font-semibold text-cyan-200">
            {{ $title }}
        </div>

        <div class="mt-4 text-sm text-slate-300">
            {{ $content }}
        </div>
    </div>

    <div class="flex flex-row justify-end gap-2 border-t border-cyan-300/20 bg-slate-900 px-6 py-4 text-end">
        {{ $footer }}
    </div>
</x-modal>

