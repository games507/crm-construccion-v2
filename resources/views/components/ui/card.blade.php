@props(['title' => null, 'subtitle' => null, 'actions' => null])

<div {{ $attributes->merge(['class' => 'bg-white/80 backdrop-blur border border-slate-200/70 rounded-2xl shadow-sm']) }}>
  @if($title || $subtitle || $actions)
    <div class="px-5 py-4 border-b border-slate-200/70 flex items-start justify-between gap-3 flex-wrap">
      <div class="min-w-0">
        @if($title)
          <h2 class="text-lg font-black text-slate-900 leading-tight truncate">{{ $title }}</h2>
        @endif
        @if($subtitle)
          <div class="text-sm font-semibold text-slate-500 mt-1">{{ $subtitle }}</div>
        @endif
      </div>
      @if($actions)
        <div class="shrink-0 flex gap-2 flex-wrap">{!! $actions !!}</div>
      @endif
    </div>
  @endif

  <div class="p-5">
    {{ $slot }}
  </div>
</div>
