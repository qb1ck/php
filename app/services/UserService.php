<?php
namespace App\services;

use App\repositories\UserRepositoryFactory;
use App\repositories\UserRepositoryInterface;

class UserService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(?string $hardSource = null)
    {
        $dotenv = \Dotenv\Dotenv::createMutable(__DIR__ . '/../../');
        $dotenv->load();

        if ($hardSource) {
            $_ENV['DB_SOURCE'] = $hardSource;
        }

        $this->userRepository = UserRepositoryFactory::getUserRepository();
    }

    public function getUsers(): array
    {
        return $this->userRepository->getUsers();
    }

    public function createUser($name, $email, $password): array
    {
        if (empty($name) || empty($email) || empty($password)) {
            return ['error' => 'Ошибка: необходимо передать name, email и password'];
        }
        return $this->userRepository->createUser($name, $email, $password);
    }

    public function deleteUser($id): array
    {
        return $this->userRepository->deleteUser($id);
    }
}
