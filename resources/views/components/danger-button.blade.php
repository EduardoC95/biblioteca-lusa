<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest text-white bg-red-600 hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 focus:ring-offset-slate-950 transition']) }}>
    {{ $slot }}
</button>

