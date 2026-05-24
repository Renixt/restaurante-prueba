<?php

namespace App\Http\Controllers\menu;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMenuItemRequest;
use App\Http\Requests\UpdateMenuItemRequest;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    public function index()
    {
        return view('content.menu.index');
    }

    public function data(Request $request)
    {
        $items = MenuItem::select(['id', 'nombre', 'descripcion', 'precio', 'categoria', 'imagen', 'disponible'])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nombre' => $item->nombre,
                    'descripcion' => $item->descripcion,
                    'precio' => number_format($item->precio, 2),
                    'categoria' => $item->categoria,
                    'imagen' => $item->imagen ? asset('storage/'.$item->imagen) : null,
                    'disponible' => $item->disponible,
                ];
            });

        return response()->json(['data' => $items]);
    }

    public function create()
    {
        return view('content.menu.create', [
            'categorias' => MenuItem::$categorias,
        ]);
    }

    public function store(StoreMenuItemRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('menu', 'public');
        }

        MenuItem::create($data);

        return redirect()->route('menu.index')
            ->with('success', 'Platillo creado correctamente.');
    }

    public function edit(MenuItem $menu_item)
    {
        return view('content.menu.edit', [
            'menuItem' => $menu_item,
            'categorias' => MenuItem::$categorias,
        ]);
    }

    public function update(UpdateMenuItemRequest $request, MenuItem $menu_item)
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            if ($menu_item->imagen) {
                Storage::disk('public')->delete($menu_item->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('menu', 'public');
        } else {
            unset($data['imagen']);
        }

        $menu_item->update($data);

        return redirect()->route('menu.index')
            ->with('success', 'Platillo actualizado correctamente.');
    }

    public function destroy(MenuItem $menu_item)
    {
        if ($menu_item->imagen) {
            Storage::disk('public')->delete($menu_item->imagen);
        }

        $menu_item->delete();

        return response()->json(['message' => 'Platillo eliminado correctamente.']);
    }
}
