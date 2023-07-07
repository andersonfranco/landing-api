<?php
declare(strict_types=1);

namespace Tests;

final class FilterTest extends ModelTestCase
{
    public function testMultipleFilters(): void
    {
        $data = $this->model->applyFilters(
            ['email' => 'JOHN@doe.com   '], 
            ['email' => 'trim|lowercase']
        );
        $this->assertSame('john@doe.com', $data['email']);
    }

    public function testTrimFilter(): void
    {
        $name = $this->model->applyFilterRule('trim', '   John Doe  ');
        $this->assertSame('John Doe', $name);
    }

    public function testLowercaseFilter(): void
    {
        $name = $this->model->applyFilterRule('lowercase', 'John Doe');
        $this->assertSame('john doe', $name);
    }

    public function testUppercaseFilter(): void
    {
        $name = $this->model->applyFilterRule('uppercase', 'John Doe');
        $this->assertSame('JOHN DOE', $name);
    }
}