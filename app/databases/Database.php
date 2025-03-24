<?php

class Database
{
    private $conn;

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $_ENV["DB_HOST"] . ";dbname=" . $_ENV["DB_DATABASE"], $_ENV["DB_USERNAME"], $_ENV["DB_PASSWORD"]);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}