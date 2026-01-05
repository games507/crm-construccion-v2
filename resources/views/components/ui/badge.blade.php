@props([
  'variant' => 'indigo', // indigo | success | danger | neutral
])

@php
  $c = match($variant) {
    'indigo'  => 'border-slate-900/10 bg-indigo-50 text-indigo-700',
    'success' => 'border-slate-900/10 bg-emerald-50 text-emerald-800',
    'danger'  => 'border-slate-900/10 bg-red-50 text-red-700',
    'neutral' => 'border-slate-900/10 bg-slate-50 text-slate-700',
    default   => 'border-slate-900/10 bg-slate-50 text-slate-700',
  };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold border '.$c]) }}>
  {{ $slot }}
</span>
