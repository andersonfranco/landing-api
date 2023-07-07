<?php

namespace App\Models;

use App\Framework\Models\Model;

return new Model(
    table: 'lead',
    // plural: 'leads',
    fields: ['nome', 'email', 'telefone', 'whatsapp', 'tipo', 'interesse'],
    filter_rules: [
        'nome' => 'uppercase',
    ],
    validation_rules: [
        'nome' => 'required|alpha_spaces',
        'email' => 'required|email',
        'telefone' => 'required_without_all:whatsapp|max:100',
        'whatsapp' => 'required_without_all:telefone|max:100',
        'tipo' => 'required',
        'interesse' => 'required',
    ]
);