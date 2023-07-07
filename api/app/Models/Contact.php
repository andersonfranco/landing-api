<?php

namespace App\Models;

use App\Framework\Models\Model;

return new Model(
    table: 'contact',
    fields: ['nome', 'email', 'mensagem', 'departamento'],
    validation_rules: [
        'nome' => 'required|alpha_spaces',
        'email' => 'required|email',
        'departamento' => 'required',
        // 'cliente' => 'required',
        'mensagem' => 'required',
    ],
);