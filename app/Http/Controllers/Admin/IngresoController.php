<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingreso;
use App\Models\Proyecto;
use Illuminate\Http\Request;

class IngresoController extends Controller
{
    private function empresaId()
    {
        return session('empresa_id') ?? auth()->user()->empresa_id;
    }

    public function index()
    {
        $empresaId = $this->empresaId();

        $ingresos = Ingreso::with('proyecto')
            ->where('empresa_id', $empresaId)
            ->latest('fecha')
            ->get();

        $proyectos = Proyecto::where('empresa_id', $empresaId)->get();

        return view('admin.ingresos.index', compact('ingresos','proyectos'));
    }

    public function store(Request $r)
    {
        $empresaId = $this->empresaId();

        $data = $r->validate([
            'proyecto_id' => ['nullable','exists:proyectos,id'],
            'descripcion' => ['nullable','string','max:200'],
            'monto'       => ['required','numeric','min:0'],
            'fecha'       => ['required','date'],
        ]);

        $data['empresa_id'] = $empresaId;
        $data['user_id'] = auth()->id();

        Ingreso::create($data);

        return back()->with('ok','Ingreso registrado');
    }
}