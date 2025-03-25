<?php

class JsonUserRepository implements UserRepositoryInterface
{
    public function getUsers(): string
    {
        if (!file_exists('users.json')) {
            die("Файл не найден.\n");
        } else {
            return file_get_contents('users.json');
        }
    }

    public function createUser($name, $email, $password): void
    {
        if (!file_exists(__DIR__ . '/../../users.json')) {
            file_put_contents(__DIR__ . '/../../users.json', json_encode([]));
        }

        $users = json_decode(file_get_contents(__DIR__ . '/../../users.json'), true);
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
        file_put_contents(__DIR__ . '/../../users.json', json_encode($users));
        unset($user['password']);
    }

    public function deleteUser($id): void
    {

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
    }
}

