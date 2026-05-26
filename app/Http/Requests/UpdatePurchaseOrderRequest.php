<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'supplier_id'              => ['required', 'exists:suppliers,id'],
            'delivery_date'            => ['nullable', 'date'],
            'notes'                    => ['nullable', 'string', 'max:1000'],
            'items'                    => ['required', 'array', 'min:1'],
            'items.*.inventory_item_id'=> ['required', 'integer', 'exists:inventory_items,id'],
            'items.*.quantity'         => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_cost'        => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required'              => 'El proveedor es obligatorio.',
            'items.required'                    => 'El pedido debe tener al menos un insumo.',
            'items.min'                         => 'El pedido debe tener al menos un insumo.',
            'items.*.inventory_item_id.required' => 'Selecciona un insumo.',
            'items.*.quantity.min'              => 'La cantidad mínima es 0.001.',
            'items.*.unit_cost.min'             => 'El costo unitario no puede ser negativo.',
        ];
    }
}
