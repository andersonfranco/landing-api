<?php

namespace App\Framework\Http;

class Header
{
    public static function cors(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit;
        }
    }

    /** @param array<string> $data */
    public static function json(array $data): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    public static function notFound(string $message = 'Not Found'): void
    {
        header("HTTP/1.1 404 $message");
        exit;
    }
}