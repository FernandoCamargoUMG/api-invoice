<?php
declare(strict_types=1);

namespace InvoiceSystem\Controllers;

use InvoiceSystem\Services\AuthService;

class AuthController {
    private AuthService $service;

    public function __construct() {
        $this->service = new AuthService();
    }

    public function register(array $request): array {
        $id = $this->service->register($request);
        return ['success' => true, 'user_id' => $id];
    }

    public function login(array $request): array {
        $response = $this->service->login($request);

        if (isset($response['user'])) {
            $user = $response['user'];

            // Genera un nuevo token de actualización
            $refreshToken = bin2hex(random_bytes(64));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));

            $db = \InvoiceSystem\Core\Database::getConnection();
            $stmt = $db->prepare("INSERT INTO refresh_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $refreshToken, $expiresAt]);

            // Devuelve todos los datos al frontend
            return [
                'success' => true,
                'access_token' => $response['token'],
                'refresh_token' => $refreshToken,
                'user' => $user
            ];
        }

        return $response;
    }
}

