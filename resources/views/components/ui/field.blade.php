@props([
  'label' => null,
  'hint' => null,
  'error' => null,
])

<div class="space-y-1">
  @if($label)
    <div class="text-sm font-extrabold text-slate-800">{{ $label }}</div>
  @endif

  {{ $slot }}

  @if($hint)
    <div class="text-xs font-semibold text-slate-500">{{ $hint }}</div>
  @endif

  @if($error)
    <div class="text-xs font-extrabold text-red-600">{{ $error }}</div>
  @endif
</div>
