<?php
namespace InvoiceSystem\Middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtMiddleware {
    public static function verify($token) {
        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
}