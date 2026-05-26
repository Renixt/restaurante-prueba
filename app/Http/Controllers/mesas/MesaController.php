<?php

namespace App\Http\Controllers\mesas;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMesaRequest;
use App\Http\Requests\UpdateMesaRequest;
use App\Models\Mesa;

class MesaController extends Controller
{
    public function index()
    {
        return view('content.mesas.index');
    }

    public function data()
    {
        $mesas = Mesa::orderBy('numero')->get()->map(function ($mesa) {
            $estadoInfo = Mesa::ESTADOS[$mesa->estado] ?? ['label' => $mesa->estado, 'class' => 'bg-label-secondary'];
            return [
                'id'        => $mesa->id,
                'numero'    => $mesa->numero,
                'capacidad' => $mesa->capacidad,
                'ubicacion' => $mesa->ubicacion ?? '—',
                'estado'    => $mesa->estado,
                'estado_label' => $estadoInfo['label'],
                'estado_class' => $estadoInfo['class'],
                'activa'    => $mesa->activa,
            ];
        });

        return response()->json(['data' => $mesas]);
    }

    public function create()
    {
        $estados = Mesa::ESTADOS;
        return view('content.mesas.create', compact('estados'));
    }

    public function store(StoreMesaRequest $request)
    {
        Mesa::create($request->validated());

        return redirect()->route('mesas.index')
            ->with('success', 'Mesa creada correctamente.');
    }

    public function edit(Mesa $mesa)
    {
        $estados = Mesa::ESTADOS;
        return view('content.mesas.edit', compact('mesa', 'estados'));
    }

    public function update(UpdateMesaRequest $request, Mesa $mesa)
    {
        $mesa->update($request->validated());

        return redirect()->route('mesas.index')
            ->with('success', 'Mesa actualizada correctamente.');
    }

    public function destroy(Mesa $mesa)
    {
        if ($mesa->orders()->whereNotIn('status', ['pagado'])->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar una mesa con órdenes activas.'
            ], 403);
        }

        $mesa->delete();

        return response()->json(['message' => 'Mesa eliminada.']);
    }
}
