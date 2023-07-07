<?php
declare(strict_types=1);

namespace Tests;

final class CreateAndRetrieveTest extends ModelTestCase
{
    /** @var array<string, mixed> */
    protected $data = [
        'name' => 'John Doe',
        'email' => 'john@doe.com',
        'message' => 'Hello World',
    ];

    public function testStore(): void
    {
        $recordId = $this->model->store($this->data);

        $this->assertIsInt($recordId);
    }

    public function testGetById(): void
    {
        $record = $this->model->getById(1);

        $this->assertSame(1, $record['id']);
        $this->assertArrayHasKey('_created_at', $record);

        unset($record['id']);
        unset($record['_created_at']);

        $this->assertSame($this->data, $record);
    }

    public function testGetAll(): void
    {
        $records = $this->model->getAll();

        $this->assertCount(1, $records);
    }
}