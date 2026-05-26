<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMesaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'numero'    => ['required', 'string', 'max:20', Rule::unique('mesas', 'numero')],
            'capacidad' => ['required', 'integer', 'min:1', 'max:50'],
            'estado'    => ['required', Rule::in(['disponible', 'ocupada', 'reservada', 'limpieza'])],
            'ubicacion' => ['nullable', 'string', 'max:100'],
            'activa'    => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['activa' => $this->boolean('activa')]);
    }

    public function messages(): array
    {
        return [
            'numero.required'   => 'El número/nombre de mesa es obligatorio.',
            'numero.unique'     => 'Ya existe una mesa con ese número.',
            'capacidad.required'=> 'La capacidad es obligatoria.',
            'capacidad.min'     => 'La capacidad mínima es 1.',
            'estado.required'   => 'El estado es obligatorio.',
            'estado.in'         => 'El estado no es válido.',
        ];
    }
}
