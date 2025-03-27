<?php
namespace App\controllers;
use App\services\UserService;

class UserController
{
    public static function index(): void
    {
        $service = new UserService();
        $users = $service->getUsers();
        echo json_encode($users);
    }

    public static function store(): void
    {
        $service = new UserService();

        global $argv;
        $name = $argv[2] ?? $_POST['name'] ?? null;
        $email = $argv[3] ?? $_POST['email'] ?? null;
        $password = $argv[4] ?? $_POST['password'] ?? null;

        $result = $service->createUser($name, $email, $password);
        echo json_encode($result);
    }

    public static function delete($id): void
    {
        $service = new UserService();
        $result = $service->deleteUser($id);
        echo json_encode($result);
    }
}
