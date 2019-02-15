<?php
declare(strict_types=1);

use CrazyGoat\Router\Configuration;
use CrazyGoat\Router\DispatcherFactory;

include '../vendor/autoload.php';

$config = new Configuration(
    'data/router-file.php',
    DispatcherFactory::defaultCollector(),
    DispatcherFactory::defaultDispatcher()

);

$dispatcher = DispatcherFactory::prepareDispatcher($config);

var_dump($dispatcher->dispatch('GET', '/users'));
