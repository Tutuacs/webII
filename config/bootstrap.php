<?php
declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $baseDir = __DIR__ . '/../';
    $paths = [
        $baseDir . 'model/' . $class . '.php',
        $baseDir . 'dao/' . $class . '.php',
        $baseDir . 'dao/mysql/' . $class . '.php',
        $baseDir . 'Service/Auth/' . $class . '.php',
        $baseDir . 'Service/Users/' . $class . '.php',
        $baseDir . 'Service/Suppliers/' . $class . '.php',
        $baseDir . 'Service/Products/' . $class . '.php',
        $baseDir . 'Service/Stock/' . $class . '.php',
        $baseDir . 'Service/Addresses/' . $class . '.php',
        $baseDir . 'Pages/Common/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (is_file($path)) {
            require_once $path;
            return;
        }
    }
});

date_default_timezone_set('America/Sao_Paulo');
