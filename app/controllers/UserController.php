<?php
class UserController
{
    public static function index(): void
    {
        $service = new UserService();
        $users = $service->getUsers();

        echo $users;
    }

    public static function store(): void
    {
        $service = new UserService();

        global $argv;
        $name = $argv[2] ?? $_POST['name'];
        $email = $argv[3] ?? $_POST['email'];
        $password = $argv[4] ?? $_POST['password'];

        if (empty($name) || empty($email) || empty($password)) {
            echo("Ошибка: необходимо передать name, email и password\n");
            return;
        }

        $service->createUser($name, $email, $password);
        echo json_encode(['success' => true]);
    }

    public static function delete($id): void
    {
        $service = new UserService();

        $service->deleteUser($id);
        echo json_encode(['success' => true]);
    }
}