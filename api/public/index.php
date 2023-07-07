<?php
declare(strict_types=1);

if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 70400) {
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode(['errors' => ['PHP version >= 7.4 required']])); 
}

require __DIR__ . '/../vendor/autoload.php';

use App\Framework\Database\Connection;
use App\Framework\Http\API;

try {
    new Connection();
    new API();
} catch (Exception $e) {
    echo $e->getMessage();
}