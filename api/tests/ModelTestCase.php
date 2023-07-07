<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Framework\Models\Model;
use App\Framework\Database\Connection;

abstract class ModelTestCase extends TestCase
{
    protected Connection $db;
    
    protected Model $model;

    /** @var array<string, mixed> */
    protected array $postData = [
        'name' => 'John Doe   ',
        'email' => '   JOHN@doe.com   ',
        'message' => 'Hello World'
    ];

    public function setUp(): void
    {
        $this->db = new Connection(databaseName: ':memory:');
        $this->model = new Model(
            table: 'newsletter',
            fields: ['name', 'email', 'message'],
            validation_rules: [
                'name' => 'required|alpha_spaces',
                'email' => 'required|email',
                'message' => 'required',
            ],
            filter_rules: [
                'name' => 'trim',
                'email' => 'lowercase|trim',
                'message' => 'uppercase'
            ]
        );
    }
}