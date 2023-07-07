<?php

namespace App\Framework\Http;

use App\Framework\Http\Controllers\ModelController;
use App\Framework\Http\Header;
use App\Framework\Models\Load;
use Pecee\Http\Request;
use Pecee\SimpleRouter\SimpleRouter as Router;

class API
{
    public function __construct()
    {
        Header::cors();
        $this->routes();
    }

    protected function routes(): void
    {
        $prefixPath = substr(dirname($_SERVER['SCRIPT_FILENAME']), strlen($_SERVER['DOCUMENT_ROOT']));
        Router::group(['prefix' => $prefixPath], function () {
            foreach (Load::allModels() as $model) {
                $modelController = new ModelController($model);
                Router::post($model->plural, function () use ($modelController) {
                    $modelController->store();
                });
            }
        });
        Router::error(function(Request $request, \Exception $exception) {
            switch($exception->getCode()) {
                case 404:
                    Header::notFound();
                    break;
                case 403:
                    Router::response()->httpCode(403);                    
                    break;
            }
        });
        Router::start();
    }
}