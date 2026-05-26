<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecipeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'recipe'                         => ['present', 'array'],
            'recipe.*.inventory_item_id'     => ['required', 'integer', 'exists:inventory_items,id'],
            'recipe.*.quantity_required'     => ['required', 'numeric', 'min:0.001'],
        ];
    }

    public function messages(): array
    {
        return [
            'recipe.*.inventory_item_id.required' => 'Selecciona un insumo.',
            'recipe.*.inventory_item_id.exists'   => 'El insumo seleccionado no existe.',
            'recipe.*.quantity_required.min'      => 'La cantidad requerida debe ser mayor a 0.',
        ];
    }
}
