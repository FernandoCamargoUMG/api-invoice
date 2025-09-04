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

$router->add('POST', '/users/refresh', function() {
    $data = json_decode(file_get_contents('php://input'), true);
    $refreshToken = $data['refresh_token'] ?? '';

    $db = \InvoiceSystem\Core\Database::getConnection();
    $stmt = $db->prepare("SELECT user_id, expires_at FROM refresh_tokens WHERE token = ?");
    $stmt->execute([$refreshToken]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$row || strtotime($row['expires_at']) < time()) {
        http_response_code(401);
        echo json_encode(['error' => 'Refresh token inválido o expirado']);
        return;
    }

    $userId = $row['user_id'];

    // Elimina el refresh token anterior
    $stmtDel = $db->prepare("DELETE FROM refresh_tokens WHERE token = ?");
    $stmtDel->execute([$refreshToken]);

    // Genera nuevo refresh token
    $nuevoRefreshToken = bin2hex(random_bytes(64));
    $nuevaExpiracion = date('Y-m-d H:i:s', strtotime('+7 days'));
    $stmtNew = $db->prepare("INSERT INTO refresh_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmtNew->execute([$userId, $nuevoRefreshToken, $nuevaExpiracion]);

    // Genera nuevo access token usando AuthService
    $authService = new \InvoiceSystem\Services\AuthService();
    $jwt = $authService->generarJWT($userId);
    header('Content-Type: application/json');
    echo json_encode([
        'access_token' => $jwt,
        'refresh_token' => $nuevoRefreshToken
    ]);
});
