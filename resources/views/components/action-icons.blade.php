@props([
  'editUrl' => null,
  'deleteUrl' => null,
  'canEdit' => true,
  'canDelete' => true,
  'confirm' => '¿Eliminar este registro?',
])

<div class="inline-flex items-center gap-1">
  @if($editUrl && $canEdit)
    <a href="{{ $editUrl }}"
       title="Editar"
       class="inline-flex items-center justify-center h-9 w-9 rounded-lg
              border border-slate-900/10 bg-white
              text-slate-600 hover:text-indigo-700 hover:border-indigo-200
              hover:bg-indigo-50 transition">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
           fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M16.862 4.487a2.25 2.25 0 1 1 3.182 3.182L7.5 20.213 3 21l.787-4.5L16.862 4.487Z"/>
      </svg>
    </a>
  @endif

  @if($deleteUrl && $canDelete)
    <form action="{{ $deleteUrl }}" method="POST" onsubmit="return confirm(@js($confirm));">
      @csrf
      @method('DELETE')
      <button type="submit"
              title="Eliminar"
              class="inline-flex items-center justify-center h-9 w-9 rounded-lg
                     border border-red-200 bg-red-50
                     text-red-600 hover:bg-red-100 hover:border-red-300 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M6 7.5h12m-9 3v6m6-6v6M9 3.75h6a1.5 1.5 0 0 1 1.5 1.5V7.5h-9V5.25A1.5 1.5 0 0 1 9 3.75Z"/>
        </svg>
      </button>
    </form>
  @endif
</div>
