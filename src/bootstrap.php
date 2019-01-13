<?php

namespace CrazyGoat\Router;

require __DIR__ . '/functions.php';

spl_autoload_register(function ($class) {
    if (strpos($class, 'CrazyGoat\\Router\\') === 0) {
        $name = substr($class, strlen('FastRoute'));
        require __DIR__ . strtr($name, '\\', DIRECTORY_SEPARATOR) . '.php';
    }
});
