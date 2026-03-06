@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'rounded-md border border-cyan-300/25 bg-slate-950/80 text-slate-100 placeholder-slate-500 shadow-sm focus:border-cyan-300 focus:ring-cyan-300']) !!}>

