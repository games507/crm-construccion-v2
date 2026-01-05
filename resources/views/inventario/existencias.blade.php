@extends('layouts.base')

@section('title', 'Existencias')
@section('page_title', 'Existencias')
@section('page_subtitle', 'Inventario actual por material y almacén')

@section('content')
  <div
    id="existencias-react"
    data-api-url="{{ route('inventario.existencias.api') }}"
  ></div>
@endsection
