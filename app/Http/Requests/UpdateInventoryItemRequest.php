<?php

namespace App\Http\Requests;

use App\Models\InventoryItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInventoryItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $itemId = $this->route('inventory')?->id;

        return [
            'name'          => ['required', 'string', 'max:255'],
            'sku'           => ['required', 'string', 'max:50', Rule::unique('inventory_items', 'sku')->ignore($itemId)],
            'unit'          => ['required', Rule::in(array_keys(InventoryItem::UNITS))],
            'current_stock' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'cost'          => ['required', 'numeric', 'min:0'],
            'supplier_id'   => ['nullable', 'exists:suppliers,id'],
            'is_active'     => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'El nombre del insumo es obligatorio.',
            'sku.required'      => 'El SKU es obligatorio.',
            'sku.unique'        => 'Ya existe un insumo con ese SKU.',
            'unit.required'     => 'La unidad de medida es obligatoria.',
            'current_stock.min' => 'El stock no puede ser negativo.',
            'minimum_stock.min' => 'El stock mínimo no puede ser negativo.',
            'cost.min'          => 'El costo no puede ser negativo.',
        ];
    }
}
