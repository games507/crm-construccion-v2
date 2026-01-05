@props([
  'type' => 'ok', // ok | err | info
])

@php
  $classes = match($type) {
    'ok'   => 'border-emerald-200 bg-emerald-50 text-emerald-800',
    'err'  => 'border-red-200 bg-red-50 text-red-800',
    'info' => 'border-slate-200 bg-slate-50 text-slate-800',
    default => 'border-slate-200 bg-slate-50 text-slate-800',
  };
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border px-4 py-3 '.$classes]) }}>
  {{ $slot }}
</div>
