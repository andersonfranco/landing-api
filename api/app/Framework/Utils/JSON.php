<?php

namespace App\Framework\Utils;

class JSON
{
    public static function isJSON(mixed $value): bool
    {
        return is_string($value) && $value === json_encode(json_decode($value));
    }
}