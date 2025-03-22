<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

class Database
{
    private $conn;

    public function __construct()
    {
        $dotenv = Dotenv::createMutable(__DIR__);
        $dotenv->load();
    }

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . getenv("DB_HOST") . ";dbname=" . getenv("DB_DATABASE"), getenv("DB_USERNAME"), getenv("DB_PASSWORD"));
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}