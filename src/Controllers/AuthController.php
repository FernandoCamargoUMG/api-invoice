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
        return $this->service->login($request);
    }
}

