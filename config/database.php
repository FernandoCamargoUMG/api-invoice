<?php
return [
    'host'   => getenv('DB_HOST') ?: 'host.docker.internal',
    'dbname' => getenv('DB_NAME') ?: 'invoice_system',
    'user'   => getenv('DB_USER') ?: 'root',
    'pass'   => getenv('DB_PASS') ?: '',
];
