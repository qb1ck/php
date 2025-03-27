<?php
namespace App\repositories;
use Exception;

class UserRepositoryFactory
{
    /**
     * @throws Exception
     */
    public static function getUserRepository(): UserRepositoryInterface
    {
        return match ($_ENV['DB_SOURCE']) {
            'json' => new JsonUserRepository(),
            'mysql' => new MysqlUserRepository(),
            default => throw new Exception("Unsupported repository type"),
        };
    }
}