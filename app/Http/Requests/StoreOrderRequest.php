<?php

namespace App\Http\Requests;

use App\Models\Mesa;
use App\Models\MenuItem;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'                   => ['required', 'in:mesa,takeaway'],
            'mesa_id'                => ['nullable', 'exists:mesas,id'],
            'notes'                  => ['nullable', 'string', 'max:500'],
            'payment_method'         => ['nullable', 'in:efectivo,tarjeta,transferencia'],
            'split_count'            => ['nullable', 'integer', 'min:1', 'max:20'],
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.menu_item_id'   => ['required', 'integer', 'exists:menu_items,id'],
            'items.*.quantity'       => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Mesa debe estar disponible
            if ($this->type === 'mesa' && $this->mesa_id) {
                $mesa = Mesa::find($this->mesa_id);
                if (!$mesa || !$mesa->isDisponible()) {
                    $validator->errors()->add('mesa_id', 'La mesa seleccionada no está disponible.');
                }
            }

            // Platillos deben estar disponibles
            if ($this->filled('items')) {
                foreach ($this->items as $idx => $item) {
                    $menuItem = MenuItem::find($item['menu_item_id'] ?? null);
                    if ($menuItem && !$menuItem->disponible) {
                        $validator->errors()->add(
                            "items.{$idx}.menu_item_id",
                            "El platillo '{$menuItem->nombre}' no está disponible."
                        );
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'type.required'                 => 'El tipo de orden es obligatorio.',
            'type.in'                       => 'El tipo debe ser mesa o takeaway.',
            'items.required'                => 'La orden debe tener al menos un platillo.',
            'items.min'                     => 'La orden debe tener al menos un platillo.',
            'items.*.menu_item_id.required' => 'Selecciona un platillo.',
            'items.*.menu_item_id.exists'   => 'El platillo seleccionado no existe.',
            'items.*.quantity.required'     => 'La cantidad es obligatoria.',
            'items.*.quantity.min'          => 'La cantidad mínima es 1.',
            'payment_method.in'             => 'Método de pago inválido.',
        ];
    }
}
