<?php

namespace App\Framework\Models;

class Load
{
    /** @return Model[] */
    public static function allModels(): array
    {
        $models = [];

        try {
            $modelsDirectory = realpath(__DIR__ . '/../../Models/') . '/*.php';
            $modelsToLoad = glob($modelsDirectory) ?: [];
            foreach ($modelsToLoad as $modelInstance) {
                $model = include $modelInstance;
                $models[$model->table] = $model;
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return $models;
    }
}