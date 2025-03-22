<?php

require_once "controllers/UserController.php";
require_once "vendor/autoload.php";

use controllers\UserController;
use Dotenv\Dotenv;

$dotenv = Dotenv::createMutable('./');
$dotenv->load();

UserController::store();