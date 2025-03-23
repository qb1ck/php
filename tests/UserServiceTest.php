<?php

use PHPUnit\Framework\TestCase;
use services\UserService;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . "/../services/UserService.php";
require_once __DIR__ . "/../Database.php";

class UserServiceTest extends TestCase
{
    // Этот метод будет выполняться перед каждым тестом
    public function setUp(): void
    {
        parent::setUp();

        // Очистка базы данных перед тестами для MySQL
        if ($_ENV['DB_SOURCE'] === 'mysql') {
            $connection = (new Database())->getConnection();
            $connection->query("DELETE FROM users WHERE email = 'test@example.com'");
            $connection->query("DELETE FROM users WHERE email = 'another@example.com'");
            $connection->query("DELETE FROM users WHERE email = 'mysqluser@example.com'");
            $connection->query("DELETE FROM users WHERE email = 'mysql2@example.com'");
        }

        // Очистка файла users.json для тестов с JSON
        if ($_ENV['DB_SOURCE'] === 'json') {
            if (file_exists('users.json')) {
                file_put_contents('users.json', json_encode([])); // Очистка содержимого файла
            }
        }
    }

    public function testCreateUser()
    {
        $userService = new UserService();

        // Тестируем для JSON
        $_ENV['DB_SOURCE'] = 'json';

        // Попытка создать пользователя с новым email
        ob_start();
        $userService->createUser('Test User', 'test@example.com', 'password123');
        $output = ob_get_clean();
        $this->assertEmpty($output); // Вывод должен быть пустым, если ошибок нет

        // Попытка создать пользователя с тем же email
        ob_start();
        $userService->createUser('Another User', 'test@example.com', 'password456');
        $output = ob_get_clean();
        $this->assertStringContainsString('Ошибка: пользователь с таким email уже существует.', $output);

        // Теперь переключаем на MySQL
        $_ENV['DB_SOURCE'] = 'mysql';

        // Попытка создать пользователя с новым email для MySQL
        ob_start();
        $userService->createUser('MySQL User', 'mysqluser@example.com', 'password789');
        $output = ob_get_clean();
        $this->assertEmpty($output); // Для MySQL результат должен быть пустым (если нет ошибок)

        // Попытка создать пользователя с тем же email для MySQL
        ob_start();
        $userService->createUser('Another User', 'mysqluser@example.com', 'password456');
        $output = ob_get_clean();
        $this->assertStringContainsString('Ошибка: пользователь с таким email уже существует.', $output);
    }

    public function testDeleteUser()
    {
        $userService = new UserService();

        // Тестируем для JSON
        $_ENV['DB_SOURCE'] = 'json';
        $userService->createUser('User to Delete', 'delete@example.com', 'password123');

        // Получаем ID созданного пользователя (это должно быть первое значение)
        $usersJson = json_decode($userService->getUsers(), true);
        $userId = $usersJson[0]['id'];  // Получаем ID первого пользователя

        // Перехватываем вывод и проверяем, что будет выведено при успешном удалении
        ob_start();
        $userService->deleteUser($userId); // Используем правильный ID
        $output = ob_get_clean();
        $this->assertStringContainsString('Пользователь успешно удален.', $output);

        // Теперь проверяем для MySQL
        $_ENV['DB_SOURCE'] = 'mysql';
        $userService->createUser('User to Delete MySQL', 'delete_mysql@example.com', 'password123');

        // Получаем ID созданного пользователя для MySQL
        $connection = (new Database())->getConnection();
        $result = $connection->query("SELECT id FROM users WHERE email = 'delete_mysql@example.com'");
        $userIdMysql = $result->fetchColumn();

        // Перехватываем вывод и проверяем, что будет выведено при успешном удалении
        ob_start();
        $userService->deleteUser($userIdMysql); // Используем правильный ID
        $output = ob_get_clean();
        $this->assertStringContainsString('Пользователь успешно удален.', $output);
    }
    public function testCreateUserDuplicateEmail()
    {
        // Устанавливаем DB_SOURCE на JSON или MySQL в зависимости от теста
        $userService = new UserService();

        // Устанавливаем источник данных в JSON для одного теста
        $_ENV['DB_SOURCE'] = 'json'; // Меняйте на 'mysql' для теста с базой данных

        // Создаем первого пользователя с данным email
        $userService->createUser('Test User', 'test@example.com', 'password123');

        // Перехватываем вывод и проверяем, что будет выведено при попытке создать пользователя с тем же email
        $this->expectOutputRegex('/Ошибка: пользователь с таким email уже существует./');

        // Попытка создать пользователя с тем же email
        $userService->createUser('Another User', 'test@example.com', 'password456');

        // Теперь проверим, что ошибка дублирования вывелась дважды
        $this->expectOutputRegex('/Ошибка: пользователь с таким email уже существует./');

        // Установим источник данных в MySQL для другого теста
        $_ENV['DB_SOURCE'] = 'mysql'; // Здесь меняем на MySQL для второго теста

        // Повторяем создание пользователя с тем же email
        $userService->createUser('Test User', 'test@example.com', 'password123');

        // Перехватываем вывод и проверяем ошибку для MySQL
        $this->expectOutputRegex('/Ошибка: пользователь с таким email уже существует./');

        // Попытка создать второго пользователя с тем же email для MySQL
        $userService->createUser('Another User', 'test@example.com', 'password456');

        // Проверяем, что вывод был корректным и соответствует ошибке дублирования.
        $this->expectOutputRegex('/Ошибка: пользователь с таким email уже существует./');
    }

    public function testGetUsers()
    {
        $userService = new UserService();

        // Для JSON
        $_ENV['DB_SOURCE'] = 'json';
        $userService->createUser('Test User 1', 'user1@example.com', 'password123');
        $userService->createUser('Test User 2', 'user2@example.com', 'password456');

        // Получаем пользователей
        $usersJson = $userService->getUsers();
        $this->assertJson($usersJson);

        // Для MySQL
        $_ENV['DB_SOURCE'] = 'mysql';
        $userService->createUser('MySQL User 1', 'mysql1@example.com', 'password789');
        $userService->createUser('MySQL User 2', 'mysql2@example.com', 'password012');

        // Получаем пользователей из MySQL
        $usersMysql = $userService->getUsers();
        $this->assertJson($usersMysql);
    }
}