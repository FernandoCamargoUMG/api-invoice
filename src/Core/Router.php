<?php

namespace InvoiceSystem\Core;

class Router {
    private $routes = [];

    public function add($method, $path, $handler) {
        $this->routes[] = compact('method', 'path', 'handler');
    }

    public function dispatch($method, $uri) {
        $uri = parse_url($uri, PHP_URL_PATH);
        foreach ($this->routes as $route) {
            $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([0-9]+)', $route['path']);
            if ($route['method'] === $method && preg_match("#^$pattern$#", $uri, $matches)) {
                array_shift($matches);
                return call_user_func_array($route['handler'], $matches);
            }
        }
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Ruta no encontrada']);
    }
}