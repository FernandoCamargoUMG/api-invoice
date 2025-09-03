<?php
use InvoiceSystem\Middlewares\JwtMiddleware;

function requireAuth() {
    $headers = getallheaders();
    $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
    if (!$token || !JwtMiddleware::verify($token)) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid token']);
        exit;
    }
}