<?php
class UserService
{
    public function __construct()
    {
        $dotenv = \Dotenv\Dotenv::createMutable(__DIR__ . '/../../');
        $dotenv->load();
    }

    public function getUsers()
    {
        if ($_ENV['DB_SOURCE'] == 'json') {
            if (!file_exists('users.json')) {
                die("Файл не найден.\n");
            } else {
                return file_get_contents('users.json');
            }
        } else {
            $users = (new Database)->getConnection()->query('SELECT * FROM users');
            $returnUsers = [];
            foreach ($users->fetchAll() as $user) {
                [$id, $name, $email, $password] = $user;
                $returnUsers[] = (new User($id, $name, $email, $password));
            }
            return json_encode($returnUsers);
        }
    }


    public function createUser($name, $email, $password)
    {
        if ($_ENV['DB_SOURCE'] == 'json') {
            if (!file_exists(__DIR__ . '/../../users.json')) {
                file_put_contents(__DIR__.'/../../users.json', json_encode([]));
            }

            $users = json_decode(file_get_contents(__DIR__ .'/../../users.json'), true);
            foreach ($users as $user) {
                if ($user['email'] == $email) {
                    echo "Ошибка: пользователь с таким email уже существует.\n";
                    return;
                }
            }

            if (count($users) !== 0) {
                $lastKey = array_key_last($users);
                $lastUser = $users[$lastKey];
                $id = $lastUser['id'] + 1;
            } else {
                $id = 1;
            }

            $user = ['id' => $id, 'name' => $name, 'email' => $email, 'password' => $password];
            $users[] = $user;
            file_put_contents(__DIR__ .'/../../users.json', json_encode($users));
            unset($user['password']);

        } else {
            $connection = (new Database())->getConnection();
            $result = $connection->query("SELECT COUNT(*) FROM users WHERE email = '$email'");
            if ($result->fetchColumn() > 0) {
                echo "Ошибка: пользователь с таким email уже существует.\n";
                return;
            }
            $connection->query("INSERT into users (name, email, password) values ('$name', '$email', '$password')");
        }
    }

    public function deleteUser($id)
    {
        if ($_ENV['DB_SOURCE'] == 'json') {
            if (!file_exists('./users.json')) {
                die("Файл не найден.\n");
            }

            $users = json_decode(file_get_contents('./users.json'), true);

            $deleted = false;

            foreach ($users as $key => $user) {
                if (isset($user['id']) && $user['id'] == $id) {
                    unset($users[$key]);
                    $deleted = true;
                    file_put_contents('./users.json', json_encode($users, JSON_PRETTY_PRINT));
                    break;
                }
            }

            if (!$deleted) {
                echo "Пользователь не найден.\n";
            } else {
                echo "Пользователь успешно удален.\n";
            }
        } else {
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

}