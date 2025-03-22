<?php

require "services/UserService.php";

use services\UserService;

$userService = new UserService();
$users = $userService->getUsers();
echo $users;
