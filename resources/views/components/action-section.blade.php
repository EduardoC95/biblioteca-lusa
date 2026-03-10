<div {{ $attributes->merge(['class' => 'md:grid md:grid-cols-[0.9fr_2.1fr] md:gap-8']) }}>
    <x-section-title>
        <x-slot name="title">{{ $title }}</x-slot>
        <x-slot name="description">{{ $description }}</x-slot>
    </x-section-title>

    <div class="mt-5 md:mt-0 w-full">
        <div class="neo-panel p-6 overflow-visible">
            {{ $content }}
        </div>
    </div>
</div>
