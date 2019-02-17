<?php

spl_autoload_register(function ($class) {
    if (strpos($class, 'CrazyGoat\\Router\\') === 0) {
        $dir = strcasecmp(substr($class, -4), 'Test') ? 'src/' : 'test/';
        $name = substr($class, strlen('CrazyGoat\\Router\\'));
        require __DIR__ . '/../' . $dir . strtr($name, '\\', DIRECTORY_SEPARATOR) . '.php';
    }
});
