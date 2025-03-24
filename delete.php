<?php
require "./controllers/UserController.php";

use app\controllers\UserController;

$id = $argv[1] ?? $_GET['id'];
UserController::delete($id);
