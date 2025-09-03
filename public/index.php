<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use InvoiceSystem\Core\Router;

$router = new Router();

require_once __DIR__ . '/../src/Routes/users.php';
require_once __DIR__ . '/../src/Routes/auth.php';
require_once __DIR__ . '/../src/Routes/products.php';
require_once __DIR__ . '/../src/Routes/customers.php';
require_once __DIR__ . '/../src/Routes/invoices.php';

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

