<?php
namespace App\repositories;
class JsonUserRepository implements UserRepositoryInterface
{
    private string $filePath;

    public function __construct()
    {
        $this->filePath = __DIR__ . '/../../users.json';
        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, json_encode([]));
        }
    }

    public function getUsers(): array
    {
        return json_decode(file_get_contents($this->filePath), true) ?? [];
    }

    public function createUser($name, $email, $password): array
    {
        $users = $this->getUsers();

        foreach ($users as $user) {
            if ($user['email'] == $email) {
                return ['error' => 'Ошибка: пользователь с таким email уже существует.'];
            }
        }

        $id = empty($users) ? 1 : end($users)['id'] + 1;
        $users[] = ['id' => $id, 'name' => $name, 'email' => $email, 'password' => $password];

        file_put_contents($this->filePath, json_encode($users, JSON_PRETTY_PRINT));
        return ['success' => true];
    }

    public function deleteUser($id): array
    {
        $users = $this->getUsers();
        foreach ($users as $key => $user) {
            if ($user['id'] == $id) {
                unset($users[$key]);
                file_put_contents($this->filePath, json_encode(array_values($users), JSON_PRETTY_PRINT));
                return ['success' => true];
            }
        }
        return ['error' => 'Пользователь не найден.'];
    }
}
