<?php

namespace App\Framework\Models;

use App\Framework\Models\Traits\ExportToCSV;
use App\Framework\Models\Traits\ExportToXLSX;
use App\Framework\Models\Traits\Validator;
use App\Framework\Utils\JSON;
use App\Framework\Utils\Str;
use RedBeanPHP as DB;

class Model
{
    use Validator, ExportToXLSX, ExportToCSV;

    /** 
     * @param array<string> $fields 
     * @param array<string, string> $filter_rules
     * @param array<string, string> $validation_rules
     * */
    public function __construct(
        public string $table = '',
        public string $plural = '',
        public array $fields = [],
        public array $filter_rules = [],
        public array $validation_rules = [],
    ) {
        $this->table = Str::filterTableName($this->table);

        if ($this->table === '') {
            throw new \Exception('Table name is not set or in wrong format. Use only letters (a-z) and numbers (0-9).');
        }       

        if ($this->plural === '') {
            $this->plural = $table . 's';
        }
    }

    /** @param array<string, mixed> $data */
    public function store(array $data): int|string
    {      
        $record = DB\R::dispense($this->table);

        foreach ($data as $key => $value) {
            $record->$key = is_array($value) ? json_encode($value) : $value;
        }

        $record->_created_at = date('Y-m-d H:i:s'); /* @phpstan-ignore-line */
        $recordId = DB\R::store($record);

        return $recordId;
    }

    /** @return array<array<string, mixed>> */
    public function getAll(): array
    {
        $records = DB\R::find($this->table);

        return [...array_map(fn($row) => array_map(fn($value) => JSON::isJSON($value) ? json_decode($value) : $value, $row->export()), $records)];
    }

    /** @return array<string, mixed> */
    public function getById(int $id): array
    {
        $record = DB\R::load($this->table, $id);
        return array_map(fn($value) => JSON::isJSON($value) ? json_decode($value) : $value, $record->export());
    }
}