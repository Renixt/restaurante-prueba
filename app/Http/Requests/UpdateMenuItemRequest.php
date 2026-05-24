<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'      => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'min:10'],
            'precio'      => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'categoria'   => ['required', 'string', 'max:100'],
            'imagen'      => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'disponible'  => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'disponible' => $this->boolean('disponible'),
        ]);
    }

    public function withValidator($validator): void
    {
        $menuItem = $this->route('menu_item');
        $validator->after(function ($validator) use ($menuItem) {
            if ($this->filled(['nombre', 'categoria'])) {
                $exists = \App\Models\MenuItem::where('nombre', $this->nombre)
                    ->where('categoria', $this->categoria)
                    ->where('id', '!=', $menuItem->id)
                    ->exists();
                if ($exists) {
                    $validator->errors()->add('nombre', 'Ya existe un platillo con ese nombre en esa categoría.');
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'nombre.required'      => 'El nombre del platillo es obligatorio.',
            'descripcion.min'      => 'La descripción debe tener al menos 10 caracteres.',
            'precio.required'      => 'El precio es obligatorio.',
            'precio.gt'            => 'El precio debe ser mayor a 0.',
            'precio.decimal'       => 'El precio no puede tener más de 2 decimales.',
            'categoria.required'   => 'La categoría es obligatoria.',
            'imagen.image'         => 'El archivo debe ser una imagen.',
            'imagen.mimes'         => 'Solo se aceptan imágenes JPG o PNG.',
            'imagen.max'           => 'La imagen no puede superar 2 MB.',
        ];
    }
}
