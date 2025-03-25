<?php

class UserService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(?string $hardSource = null)
    {
        $dotenv = \Dotenv\Dotenv::createMutable(__DIR__ . '/../../');
        $dotenv->load();
        if($hardSource){
            $_ENV['DB_SOURCE'] = $hardSource;
        }

        $this->userRepository = UserRepositoryFactory::getUserRepository();
    }

    public function getUsers()
    {
        return $this->userRepository->getUsers();
    }


    public function createUser($name, $email, $password)
    {
        return $this->userRepository->createUser($name, $email, $password);
    }

    public function deleteUser($id)
    {
        return $this->userRepository->deleteUser($id);
    }

}