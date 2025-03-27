<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Console;
use App\controllers\UserController;
use App\Router;

// Обработка CLI-команд
if (php_sapi_name() === 'cli') {
    $console = new Console();
    global $argv;
    $console->handle($argv);
    exit;
}

// Инициализация роутера для веб-запросов
$router = new Router();

$router->add('GET', '/users-list', [UserController::class, 'index']);
$router->add('DELETE', '/delete-user/{id}', [UserController::class, 'delete']);
$router->add('POST', '/user-create', [UserController::class, 'store']);

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

$router->dispatch($requestUri, $requestMethod);