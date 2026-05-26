<?php

namespace App\Http\Controllers\inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRecipeRequest;
use App\Models\InventoryItem;
use App\Models\MenuItem;
use App\Models\Recipe;

class RecipeController extends Controller
{
    public function index()
    {
        $menuItems = MenuItem::with('recipes')->orderBy('categoria')->orderBy('nombre')->get();
        $inventoryItems = InventoryItem::where('is_active', true)->orderBy('name')->get();
        return view('content.recipes.index', compact('menuItems', 'inventoryItems'));
    }

    public function edit(MenuItem $menuItem)
    {
        $menuItem->load('recipes.inventoryItem');
        $inventoryItems = InventoryItem::where('is_active', true)->orderBy('name')->get();

        $inventoryData = $inventoryItems->keyBy('id')->map(fn($i) => [
            'id'   => $i->id,
            'name' => $i->name,
            'unit' => $i->unit,
        ]);

        return view('content.recipes.edit', compact('menuItem', 'inventoryItems', 'inventoryData'));
    }

    public function update(StoreRecipeRequest $request, MenuItem $menuItem)
    {
        $data = $request->validated();

        Recipe::where('menu_item_id', $menuItem->id)->delete();

        if (!empty($data['recipe'])) {
            foreach ($data['recipe'] as $item) {
                Recipe::create([
                    'menu_item_id'       => $menuItem->id,
                    'inventory_item_id'  => $item['inventory_item_id'],
                    'quantity_required'  => $item['quantity_required'],
                ]);
            }
        }

        return redirect()->route('recipes.index')
            ->with('success', "Receta de '{$menuItem->nombre}' guardada correctamente.");
    }
}
