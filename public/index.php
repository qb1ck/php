<?php

use controllers\UserController;

require "../Router.php";
require "../controllers/UserController.php";
// Инициализация роутера
$router = new Router();

// Определение маршрутов
$router->add('GET', '/users-list', [UserController::class, 'index']);
$router->add('DELETE', '/delete-user/{id}', [UserController::class, 'delete']);
$router->add('POST', '/user-create', [UserController::class, 'store']);

// Отправка запроса
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

$router->dispatch($requestUri, $requestMethod);