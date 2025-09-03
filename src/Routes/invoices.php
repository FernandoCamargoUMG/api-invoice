<?php

use InvoiceSystem\Controllers\InvoiceController;
require_once __DIR__ . '/../Core/AuthHelper.php';

$invoiceController = new InvoiceController();

$router->add('GET', '/invoices', function() use ($invoiceController) {
    requireAuth();
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
    header('Content-Type: application/json');
    echo json_encode($invoiceController->index($limit, $offset));
});

$router->add('POST', '/invoices', function() use ($invoiceController) {
    requireAuth();
    $data = json_decode(file_get_contents('php://input'), true);
    header('Content-Type: application/json');
    echo json_encode($invoiceController->store($data));
});

$router->add('GET', '/invoices/{id}', function($id) use ($invoiceController) {
    requireAuth();
    header('Content-Type: application/json');
    echo json_encode($invoiceController->show($id));
});

$router->add('PUT', '/invoices/{id}', function($id) use ($invoiceController) {
    requireAuth();
    $data = json_decode(file_get_contents('php://input'), true);
    header('Content-Type: application/json');
    echo json_encode($invoiceController->update($id, $data));
});

$router->add('DELETE', '/invoices/{id}', function($id) use ($invoiceController) {
    requireAuth();
    header('Content-Type: application/json');
    echo json_encode($invoiceController->destroy($id));
});