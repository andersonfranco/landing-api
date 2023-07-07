<?php

namespace App\Framework\Http\Controllers;

use App\Framework\Http\Header;
use App\Framework\Models\Model;
use Pecee\SimpleRouter\SimpleRouter as Router;

class ModelController
{
    public function __construct(protected Model $model) {}

    public function exportToXLSX(): void
    {
        $this->model->exportToXLSX();
    }

    public function exportToCSV(): void
    {
        $this->model->exportToCSV();
    }

    public function getAll(): void
    {
        Router::response()->json($this->model->getAll());
    }

    public function getById(int|string $id): void
    {
        $notFoundMessage = "User $id does not exist";
        if (preg_match('/^\d+$/', (string) $id) !== 1) Header::notFound($notFoundMessage);

        $record = $this->model->getById((int) $id);
        if ($record['id'] === 0) Header::notFound($notFoundMessage);

        Router::response()->json($record);
    }

    public function store(): void
    {
        [$errors, $validData] = $this->model->filterAndValidate($_POST);
        
        if ($validData) {
            $recordId = $this->model->store($validData);
            if ($recordId) {
                Router::response()->json(['success' => true]);
            }
        }

        Router::response()->json(['success' => false, 'errors' => $errors]);
    }
}