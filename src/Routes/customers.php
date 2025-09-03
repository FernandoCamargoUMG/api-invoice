<?php

use InvoiceSystem\Controllers\CustomerController;

require_once __DIR__ . '/../Core/AuthHelper.php';

$customerController = new CustomerController();

$router->add('GET', '/customers', function() use ($customerController) {
    requireAuth();
    header('Content-Type: application/json');
    echo json_encode($customerController->index());
});

$router->add('POST', '/customers', function() use ($customerController) {
    requireAuth();
    $data = json_decode(file_get_contents('php://input'), true);
    header('Content-Type: application/json');
    echo json_encode($customerController->store($data));
});

$router->add('GET', '/customers/{id}', function($id) use ($customerController) {
    requireAuth();
    header('Content-Type: application/json');
    echo json_encode($customerController->show($id));
});

$router->add('PUT', '/customers/{id}', function($id) use ($customerController) {
    requireAuth();
    $data = json_decode(file_get_contents('php://input'), true);
    header('Content-Type: application/json');
    echo json_encode($customerController->update($id, $data));
});

$router->add('DELETE', '/customers/{id}', function($id) use ($customerController) {
    requireAuth();
    header('Content-Type: application/json');
    echo json_encode($customerController->destroy($id));
});