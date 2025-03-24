<?php

require "services/UserService.php";

use app\services\UserService;

$userService = new UserService();
$users = $userService->getUsers();
echo $users;
