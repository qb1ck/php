<?php

class MysqlUserRepository implements UserRepositoryInterface
{
    public function __construct()
    {
        $connection = (new Database())->getConnection();
        if (!count($connection->query("SHOW TABLES LIKE 'users'")->fetchAll())) {
            $connection->query("CREATE TABLE `users` (id int primary key auto_increment, name varchar(255), email varchar(255), password varchar(255))");
        }
    }

    public function getUsers(): string
    {
        $users = (new Database)->getConnection()->query('SELECT * FROM users');
        $returnUsers = [];
        foreach ($users->fetchAll() as $user) {
            [$id, $name, $email, $password] = $user;
            $returnUsers[] = (new User($id, $name, $email, $password));
        }
        return json_encode($returnUsers);
    }

    public function createUser($name, $email, $password): void
    {
        $connection = (new Database())->getConnection();
        $result = $connection->query("SELECT COUNT(*) FROM users WHERE email = '$email'");
        if ($result->fetchColumn() > 0) {
            echo "Ошибка: пользователь с таким email уже существует.\n";
            return;
        }
        $connection->query("INSERT into users (name, email, password) values ('$name', '$email', '$password')");
    }

    public function deleteUser($id): void
    {
        $connection = (new Database())->getConnection();
        $tableExists = $connection->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
        if (!$tableExists) {
            echo "Таблица не существует.\n";
            return;
        }

        $result = $connection->query("SELECT COUNT(*) FROM users WHERE id = '$id'");
        if ($result->fetchColumn() == 0) {
            echo "Пользователь не найден.\n";
            return;
        }

        $connection->query("DELETE FROM users WHERE id = '$id'");
        echo "Пользователь успешно удален.\n";
    }
}