@props(['for'])

@error($for)
    <p {{ $attributes->merge(['class' => 'text-sm text-red-300']) }}>{{ $message }}</p>
@enderror

