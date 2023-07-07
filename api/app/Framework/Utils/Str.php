<?php

namespace App\Framework\Utils;

class Str
{
    public static function filterTableName(string $str): string
    {
        return strtolower(preg_replace('/[^A-Za-z0-9]/', '', $str) ?: '');
    }
}