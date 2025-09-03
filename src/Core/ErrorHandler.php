<?php
declare(strict_types=1);

namespace InvoiceSystem\Core;

final class ErrorHandler
{
    public static function register(): void
    {
        set_exception_handler([self::class, 'handleException']);
    }

    public static function handleException(\Throwable $e): void
    {
        http_response_code($e->getCode() >= 400 ? $e->getCode() : 500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'error' => true,
            'message' => $e->getMessage()
        ]);
    }
}
