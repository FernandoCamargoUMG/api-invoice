<?php

use InvoiceSystem\Controllers\AuthController;

$authController = new AuthController();

$router->add('POST', '/users/login', function() use ($authController) {
    $data = json_decode(file_get_contents('php://input'), true);
    header('Content-Type: application/json');
    echo json_encode($authController->login($data));
});