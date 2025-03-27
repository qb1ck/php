<?php
namespace App\repositories;
use App\databases\Database;


class MysqlUserRepository implements UserRepositoryInterface
{
    private ?\PDO $connection;

    public function __construct()
    {
        $this->connection = (new Database())->getConnection();
        $this->connection->query("CREATE TABLE IF NOT EXISTS `users` (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(255),
            email VARCHAR(255) UNIQUE,
            password VARCHAR(255)
        )");
    }

    public function getUsers(): array
    {
        return $this->connection->query('SELECT * FROM users')->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createUser(string $name, string  $email, string  $password): array
    {
        $result = $this->connection->query("SELECT COUNT(*) FROM users WHERE email = '$email'");
        if ($result->fetchColumn() > 0) {
            return ['error' => 'Ошибка: пользователь с таким email уже существует.'];
        }

        $this->connection->query("INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')");
        return ['success' => true];
    }

    public function deleteUser(int $id): array
    {
        $result = $this->connection->query("SELECT COUNT(*) FROM users WHERE id = '$id'");
        if ($result->fetchColumn() == 0) {
            return ['error' => 'Пользователь не найден.'];
        }

        $this->connection->query("DELETE FROM users WHERE id = '$id'");
        return ['success' => true];
    }
}
