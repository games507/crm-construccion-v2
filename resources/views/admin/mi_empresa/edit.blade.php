@extends('layouts.base')
@section('title','Mi Empresa')

@section('content')
@php
  /** @var \App\Models\Empresa $empresa */
  $logoUrl = null;

  // Si ya tienes guardado el logo en BD como "logo_path" (ej: empresas/1/logo.png)
  if (!empty($empresa->logo_path ?? null)) {
    $logoUrl = asset('storage/' . ltrim($empresa->logo_path, '/'));
  }
@endphp

<div class="max-w-5xl mx-auto">

  {{-- Header --}}
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-extrabold tracking-tight text-slate-900">Mi Empresa</h1>
      <p class="text-sm text-slate-500 mt-1">
        Actualiza la información y el logo que se mostrará en el menú.
      </p>
    </div>

    <a href="{{ route('dashboard') }}"
       class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-semibold
              bg-white border border-slate-200 hover:border-slate-300 shadow-sm">
      ← Volver
    </a>
  </div>

  {{-- Alerts --}}
  @if(session('ok'))
    <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 font-semibold">
      ✅ {{ session('ok') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
      <div class="font-extrabold">Hay errores</div>
      <ul class="list-disc ml-6 mt-2 text-sm">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Card --}}
  <form method="POST" action="{{ route('admin.mi_empresa.update') }}" enctype="multipart/form-data"
        class="mt-5 rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    @csrf
    @method('PUT')

    <div class="p-5 border-b border-slate-200/70">
      <div class="text-sm font-extrabold text-slate-900">Información</div>
      <div class="text-xs font-bold text-slate-500 mt-1">Datos básicos de tu empresa</div>
    </div>

    <div class="p-5 grid grid-cols-1 md:grid-cols-12 gap-4">

      {{-- Nombre --}}
      <div class="md:col-span-7">
        <label class="text-xs font-extrabold text-slate-600">Nombre</label>
        <input
          name="nombre"
          value="{{ old('nombre', $empresa->nombre) }}"
          required
          class="mt-2 w-full h-11 rounded-xl border border-slate-200 px-3 bg-white
                 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none"
          placeholder="Ej: Constructora ABC, S.A."
        >
      </div>

      {{-- RUC/NIT (si lo tienes) --}}
      <div class="md:col-span-5">
        <label class="text-xs font-extrabold text-slate-600">RUC / NIT (opcional)</label>
        <input
          name="ruc"
          value="{{ old('ruc', $empresa->ruc ?? '') }}"
          class="mt-2 w-full h-11 rounded-xl border border-slate-200 px-3 bg-white
                 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none"
          placeholder="Ej: 123456-1-999999"
        >
      </div>

      {{-- Teléfono --}}
      <div class="md:col-span-4">
        <label class="text-xs font-extrabold text-slate-600">Teléfono (opcional)</label>
        <input
          name="telefono"
          value="{{ old('telefono', $empresa->telefono ?? '') }}"
          class="mt-2 w-full h-11 rounded-xl border border-slate-200 px-3 bg-white
                 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none"
          placeholder="Ej: 6000-0000"
        >
      </div>

      {{-- Correo --}}
      <div class="md:col-span-8">
        <label class="text-xs font-extrabold text-slate-600">Correo (opcional)</label>
        <input
          type="email"
          name="correo"
          value="{{ old('correo', $empresa->correo ?? '') }}"
          class="mt-2 w-full h-11 rounded-xl border border-slate-200 px-3 bg-white
                 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none"
          placeholder="Ej: compras@empresa.com"
        >
      </div>

      {{-- Logo --}}
      <div class="md:col-span-12">
        <div class="mt-2 rounded-2xl border border-slate-200 bg-slate-50 p-4">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
              <div class="text-sm font-extrabold text-slate-900">Logo</div>
              <div class="text-xs font-bold text-slate-500 mt-1">
                PNG/JPG recomendado (fondo transparente si puedes). Máx 2MB.
              </div>
            </div>

            {{-- Preview actual --}}
            <div class="flex items-center gap-3">
              <div class="h-14 w-14 rounded-2xl bg-white border border-slate-200 grid place-items-center overflow-hidden shadow-sm">
                @if($logoUrl)
                  <img src="{{ $logoUrl }}" alt="Logo" class="h-10 w-auto object-contain">
                @else
                  <span class="text-slate-400 font-black">
                    {{ strtoupper(substr($empresa->nombre ?? 'E', 0, 1)) }}
                  </span>
                @endif
              </div>
              <div class="text-xs font-bold text-slate-500">
                Vista previa
              </div>
            </div>
          </div>

          <div class="mt-4 grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
            <div class="md:col-span-8">
              <input
                id="logo"
                type="file"
                name="logo"
                accept="image/png,image/jpeg,image/jpg,image/webp"
                class="block w-full text-sm text-slate-700
                       file:mr-4 file:rounded-xl file:border-0 file:px-4 file:py-2
                       file:bg-indigo-600 file:text-white file:font-extrabold
                       hover:file:bg-indigo-700"
                onchange="previewLogo(event)"
              >
              <div class="text-xs font-bold text-slate-500 mt-2">
                Si subes uno nuevo, reemplaza el anterior.
              </div>
            </div>

            <div class="md:col-span-4">
              <div class="h-16 rounded-2xl bg-white border border-slate-200 flex items-center justify-center overflow-hidden shadow-sm">
                <img id="logoPreview" src="{{ $logoUrl ?? '' }}" class="h-12 w-auto object-contain {{ $logoUrl ? '' : 'hidden' }}" alt="Preview">
                <div id="logoPlaceholder" class="text-xs font-extrabold text-slate-400 {{ $logoUrl ? 'hidden' : '' }}">
                  Preview aquí
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

    </div>

    {{-- Footer actions --}}
    <div class="p-5 border-t border-slate-200/70 bg-white flex items-center justify-end gap-2">
      <a href="{{ route('dashboard') }}"
         class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-semibold
                bg-white border border-slate-200 hover:border-slate-300 shadow-sm">
        Cancelar
      </a>
      <button type="submit"
              class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-extrabold
                     bg-slate-900 text-white hover:bg-slate-800 shadow-sm">
        Guardar cambios
      </button>
    </div>
  </form>

</div>

@push('scripts')
<script>
function previewLogo(e){
  const file = e.target.files?.[0];
  const img = document.getElementById('logoPreview');
  const ph  = document.getElementById('logoPlaceholder');
  if(!file){ return; }
  const url = URL.createObjectURL(file);
  img.src = url;
  img.classList.remove('hidden');
  ph.classList.add('hidden');
}
</script>
@endpush
@endsection
