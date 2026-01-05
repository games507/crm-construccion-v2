@props(['icon' => null])

<div class="relative">
  @if($icon)
    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
      {!! $icon !!}
    </div>
  @endif

  <input {{ $attributes->merge([
    'class' => ($icon ? 'pl-10 ' : 'pl-4 ') .
      'w-full h-11 rounded-xl bg-white border border-slate-200/70 shadow-sm
       focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300
       text-sm font-semibold text-slate-900 placeholder:text-slate-400'
  ]) }}>
</div>
