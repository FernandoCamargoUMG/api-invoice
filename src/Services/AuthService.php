<?php
declare(strict_types=1);

namespace InvoiceSystem\Services;

use InvoiceSystem\Models\UserModel;
use Firebase\JWT\JWT;

class AuthService {
    public function login(array $data): array {
        $user = UserModel::getByEmail($data['email']);
        if ($user && $user['password'] === md5($data['password'])) {
            $payload = [
                'sub' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role'],
                'iat' => time(),
                'exp' => time() + 3600 // 1 hora
            ];
            $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
            unset($user['password']);
            return ['success' => true, 'token' => $jwt, 'user' => $user];
        }
        return ['success' => false, 'message' => 'Credenciales inválidas'];
    }

    public function register(array $data): int {
        $result = \InvoiceSystem\Models\UserModel::create($data);
        return $result['id'];
    }
}
