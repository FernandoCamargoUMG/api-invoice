<?php

use InvoiceSystem\Controllers\UserController;

require_once __DIR__ . '/../Core/AuthHelper.php';

$userController = new UserController();

$router->add('GET', '/users', function() use ($userController) {
    requireAuth();
    header('Content-Type: application/json');
    echo json_encode($userController->index());
});

$router->add('POST', '/users', function() use ($userController) {
    requireAuth();
    $data = json_decode(file_get_contents('php://input'), true);
    header('Content-Type: application/json');
    echo json_encode($userController->store($data));
});

$router->add('GET', '/users/{id}', function($id) use ($userController) {
    requireAuth();
    header('Content-Type: application/json');
    echo json_encode($userController->show($id));
});

$router->add('PUT', '/users/{id}', function($id) use ($userController) {
    requireAuth();
    $data = json_decode(file_get_contents('php://input'), true);
    header('Content-Type: application/json');
    echo json_encode($userController->update($id, $data));
});

$router->add('DELETE', '/users/{id}', function($id) use ($userController) {
    requireAuth();
    header('Content-Type: application/json');
    echo json_encode($userController->destroy($id));
});
