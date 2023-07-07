<?php

namespace App\Framework\Models\Traits;

use Somnambulist\Components\Validation\Factory as ValidatorFactory;

trait Validator
{
    public function applyFilterRule(string $filter, string $value): string
    {
        return match ($filter) {
            'trim' => trim($value),
            'uppercase' => mb_strtoupper($value),
            'lowercase' => mb_strtolower($value),
            default => $value
        };
    }

    /** 
     * @param array<string, mixed> $data
     * @param array<string, string> $rules
     * @return array<string, mixed>
     * */
    public function applyFilters(array $data, array $rules): array
    {
        foreach ($rules as $attribute => $filters) {
            foreach (explode('|', $filters) as $filter) {
                if (isset($data[$attribute]) && is_string($data[$attribute])) {
                    $data[$attribute] = $this->applyFilterRule($filter, $data[$attribute]);
                }
            }
        }
        return $data;
    }

    /** 
     * @param array<string, mixed> $data
     * @param array<string, string> $rules
     * @return array<string>
     * */
    public function validate(array &$data, array &$rules): array
    {
        $validator = new ValidatorFactory();
        $validator->messages()->replace('en', 'rule.required', 'O campo :attribute é obrigatório');
        $validator->messages()->replace('en', 'rule.email', 'O campo :attribute deve ser válido');
        $validator->messages()->replace('en', 'rule.required_without', 'Selecione uma ou mais das opções disponíveis');
        $validator->messages()->replace('en', 'rule.required_without_all', 'Selecione uma ou mais das opções disponíveis');

        $validation = $validator->validate($data, $rules);

        if ($validation->fails()) {
            $errors = $validation->errors()->firstOfAll();
            return $errors;
        }

        return [];
    }

    /** 
     * @param array<string, mixed> $data 
     * @return array{array<string>, array<string, mixed>}
     * */
    public function filterAndValidate(array &$data): array
    {
        $fieldKeys = array_flip($this->fields);
        $validData = array_filter($data, fn($key) => isset($fieldKeys[$key]), ARRAY_FILTER_USE_KEY);

        $validData = $this->applyFilters($validData, $this->filter_rules);

        $errors = $this->validate($validData, $this->validation_rules);

        if ($errors) {
            return [$errors, []];
        }

        return [[], $validData];
    }
}