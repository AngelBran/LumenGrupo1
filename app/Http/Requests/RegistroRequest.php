<?php

namespace App\Http\Requests;

use Anik\Form\FormRequest;

class RegistroRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            "names" => ['required', 'string'],
            "lastnames" => ['required', 'string'],
            "username" => ['required', 'string'],
            "email" => ['required', 'string', 'email', 'unique:users'],
            "names" => ['required', 'string'],
            "birthday" => ['required', 'date'],
            "phone" => ['required', 'integer', 'unique:users']
        ];
    }

    public function messages(): array {
        return [
            'email.required' => "El correo electrónico es obligatorio",
            'email.email' => "El correo electrónico es incorrecto",
            'email.unique' => "El correo electrónico ya está en uso",
            'phone.required' => "El número de teléfono es obligatorio",
            'phone.email' => "El número de teléfono es incorrecto",
            'phone.unique' => "El número de teléfono ya está en uso",
        ];
    }
}
