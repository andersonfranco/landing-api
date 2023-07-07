<?php
declare(strict_types=1);

namespace Tests;

final class ValidationTest extends ModelTestCase
{
    public function testValidateSuccess(): void
    {       
        [$errors, $validData] = $this->model->filterAndValidate($this->postData);

        $this->assertNotEmpty($validData, implode(', ', $errors));
    }

    public function testValidateError(): void
    {       
        $postData = [];

        [$errors] = $this->model->filterAndValidate($postData);

        $this->assertNotEmpty($errors, implode(', ', $errors));
    }
}