<?php

namespace App\Http\Requests;

use App\Models\Mesa;
use App\Models\MenuItem;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
        $order = $this->route('order');

        $validator->after(function ($validator) use ($order) {
            // Mesa disponible (ignorar orden actual)
            if ($this->type === 'mesa' && $this->mesa_id) {
                $mesa = Mesa::find($this->mesa_id);
                if (!$mesa || !$mesa->activa) {
                    $validator->errors()->add('mesa_id', 'La mesa seleccionada no existe o está inactiva.');
                } else {
                    $occupied = $mesa->orders()
                        ->where('id', '!=', $order->id)
                        ->whereNotIn('status', ['pagado'])
                        ->exists();
                    if ($occupied) {
                        $validator->errors()->add('mesa_id', 'La mesa ya tiene una orden activa.');
                    }
                }
            }

            // Platillos disponibles
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
            'items.required'                => 'La orden debe tener al menos un platillo.',
            'items.min'                     => 'La orden debe tener al menos un platillo.',
            'items.*.menu_item_id.required' => 'Selecciona un platillo.',
            'items.*.quantity.min'          => 'La cantidad mínima es 1.',
            'payment_method.in'             => 'Método de pago inválido.',
        ];
    }
}
