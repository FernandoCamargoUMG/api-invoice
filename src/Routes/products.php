<?php

use InvoiceSystem\Controllers\ProductController;
require_once __DIR__ . '/../Core/AuthHelper.php';

$productController = new ProductController();

$router->add('GET', '/products', function() use ($productController) {
    requireAuth();
    header('Content-Type: application/json');
    echo json_encode($productController->index());
});

$router->add('POST', '/products', function() use ($productController) {
    requireAuth();
    $data = json_decode(file_get_contents('php://input'), true);
    header('Content-Type: application/json');
    echo json_encode($productController->store($data));
});

$router->add('GET', '/products/{id}', function($id) use ($productController) {
    requireAuth();
    header('Content-Type: application/json');
    echo json_encode($productController->show($id));
});

$router->add('PUT', '/products/{id}', function($id) use ($productController) {
    requireAuth();
    $data = json_decode(file_get_contents('php://input'), true);
    header('Content-Type: application/json');
    echo json_encode($productController->update($id, $data));
});

$router->add('DELETE', '/products/{id}', function($id) use ($productController) {
    requireAuth();
    header('Content-Type: application/json');
    echo json_encode($productController->destroy($id));
});