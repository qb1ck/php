<?php

use database\Database;

require "Database.php";

$users= (new Database())->getConnection()->query('SELECT * FROM users');
foreach ($users->fetchAll() as $user) {
    [$id, $name, $email, $password] = $user;
}
