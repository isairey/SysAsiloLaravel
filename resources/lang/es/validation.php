<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Líneas de validación en español
    |--------------------------------------------------------------------------
    |
    | Las siguientes líneas contienen los mensajes de error predeterminados usados
    | por la clase validadora de Laravel. Algunas de estas reglas tienen varias
    | versiones como las reglas de tamaño. Puedes modificar estos mensajes aquí.
    |
    */

    'accepted' => 'El campo :attribute debe ser aceptado.',
    'active_url' => 'El campo :attribute no es una URL válida.',
    'after' => 'El campo :attribute debe ser una fecha posterior a :date.',
    'alpha' => 'El campo :attribute solo debe contener letras.',
    'alpha_num' => 'El campo :attribute solo debe contener letras y números.',
    'array' => 'El campo :attribute debe ser un arreglo.',
    'before' => 'El campo :attribute debe ser una fecha anterior a :date.',
    'between' => [
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'file' => 'El archivo :attribute debe pesar entre :min y :max kilobytes.',
        'string' => 'El campo :attribute debe tener entre :min y :max caracteres.',
        'array' => 'El campo :attribute debe tener entre :min y :max elementos.',
    ],
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'date' => 'El campo :attribute no es una fecha válida.',
    'email' => 'El campo :attribute debe ser una dirección de correo electrónico válida.',
    'exists' => 'El campo :attribute seleccionado no es válido.',
    'image' => 'El campo :attribute debe ser una imagen.',
    'in' => 'El campo :attribute seleccionado no es válido.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'max' => [
        'string' => 'El campo :attribute no debe tener más de :max caracteres.',
    ],
    'min' => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'numeric' => 'El campo :attribute debe ser un número.',
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser una cadena de texto.',
    'unique' => 'El campo :attribute ya está en uso.',
    'url' => 'El campo :attribute no es una URL válida.',

    /*
    |--------------------------------------------------------------------------
    | Personalización de atributos
    |--------------------------------------------------------------------------
    |
    | Aquí puedes especificar nombres personalizados para los campos.
    |
    */

    'attributes' => [
        'nombre' => 'nombre',
        'apellido' => 'apellido',
        'ci' => 'cédula de identidad',
        'telefono' => 'teléfono',
        'direccion' => 'dirección',
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'password_confirmation' => 'confirmación de contraseña',
    ],
];