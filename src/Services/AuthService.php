<?php

declare(strict_types=1);

namespace InvoiceSystem\Services;

use InvoiceSystem\Models\UserModel;
use Firebase\JWT\JWT;

class AuthService
{
    public function generarJWT($userId): string
    {
        $user = \InvoiceSystem\Models\UserModel::getById($userId);
        $payload = [
            'sub' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'iat' => time(),
            'exp' => time() + 3600
        ];
        return \Firebase\JWT\JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
    }
    public function login(array $data): array
    {
        $user = UserModel::getByEmail($data['email']);
        if ($user && $user['password'] === md5($data['password'])) {
            $jwt = $this->generarJWT($user['id']);
            $refreshToken = bin2hex(random_bytes(64));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));

            $db = \InvoiceSystem\Core\Database::getConnection();
            $stmt = $db->prepare("INSERT INTO refresh_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $refreshToken, $expiresAt]);

            unset($user['password']);
            return [
                'success' => true,
                'token' => $jwt,
                'refresh_token' => $refreshToken,
                'user' => $user
            ];
        }
        return ['success' => false, 'message' => 'Credenciales inválidas'];
    }

    public function register(array $data): int
    {
        $result = \InvoiceSystem\Models\UserModel::create($data);
        return $result['id'];
    }
}
