<?php

interface UserRepositoryInterface {
    public function getUsers();
    public function createUser(string $name, string $email, string $password);
    public function deleteUser(int $id);
}