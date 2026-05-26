<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'business_name' => ['required', 'string', 'max:255'],
            'rfc'           => ['required', 'string', 'regex:/^[A-ZÑ&]{3,4}\d{6}[A-Z\d]{3}$/i', 'max:13'],
            'phone'         => ['nullable', 'regex:/^\d{7,15}$/', 'max:20'],
            'email'         => ['nullable', 'email', 'max:255'],
            'address'       => ['nullable', 'string', 'max:500'],
            'status'        => ['required', Rule::in(['activo', 'inactivo'])],
        ];
    }

    public function messages(): array
    {
        return [
            'business_name.required' => 'La razón social es obligatoria.',
            'rfc.required'           => 'El RFC es obligatorio.',
            'rfc.regex'              => 'El RFC no tiene un formato válido (ej. XAXX010101000).',
            'phone.regex'            => 'El teléfono debe contener solo dígitos (7-15).',
            'email.email'            => 'El correo electrónico no es válido.',
        ];
    }
}
