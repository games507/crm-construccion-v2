@props(['variant' => 'primary', 'type' => 'button'])

@php
  $base = 'inline-flex items-center justify-center gap-2 rounded-xl px-4 h-11 text-sm font-extrabold shadow-sm active:scale-[.99] transition';

  $styles = [
    'primary' => 'bg-indigo-600 text-white hover:bg-indigo-700',
    'outline' => 'bg-white text-slate-800 border border-slate-200/70 hover:bg-slate-50',
    'danger'  => 'bg-red-600 text-white hover:bg-red-700',
    // opcionales (por si en algún lado los llaman)
    'success' => 'bg-emerald-600 text-white hover:bg-emerald-700',
    'dark'    => 'bg-slate-900 text-white hover:bg-slate-800',
  ];

  // Si llega un variant no existente, cae a primary (evita errores)
  $variant = array_key_exists($variant, $styles) ? $variant : 'primary';
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $base.' '.$styles[$variant]]) }}>
  {{ $slot }}
</button>
