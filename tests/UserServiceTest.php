<?php

use App\databases\Database;
use App\services\UserService;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';

class UserServiceTest extends TestCase
{
    private UserService $jsonService;
    private UserService $mysqlService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->jsonService = new UserService('json');
        $this->mysqlService = new UserService('mysql');

        $connection = (new Database())->getConnection();
        $connection->query("DELETE FROM users WHERE email LIKE 'test%'");

        file_put_contents(__DIR__ . '/../users.json', json_encode([]));
    }

    public function testCreateUser()
    {
        $this->assertEquals(['success' => true], $this->jsonService->createUser('Test', 'test@example.com', 'pass123'));
        $this->assertEquals(['error' => 'Ошибка: пользователь с таким email уже существует.'], $this->jsonService->createUser('Test', 'test@example.com', 'pass123'));

        $this->assertEquals(['success' => true], $this->mysqlService->createUser('Test', 'test@example.com', 'pass123'));
        $this->assertEquals(['error' => 'Ошибка: пользователь с таким email уже существует.'], $this->mysqlService->createUser('Test', 'test@example.com', 'pass123'));
    }

    public function testDeleteUser()
    {
        $this->jsonService->createUser('Test', 'test@example.com', 'pass123');
        $users = $this->jsonService->getUsers();
        $id = $users[0]['id'];

        $this->assertEquals(['success' => true], $this->jsonService->deleteUser($id));
        $this->assertEquals(['error' => 'Пользователь не найден.'], $this->jsonService->deleteUser($id));
    }
}
