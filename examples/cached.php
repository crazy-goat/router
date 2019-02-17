<?php
declare(strict_types=1);

use CrazyGoat\Router\Configuration;
use CrazyGoat\Router\DispatcherFactory;
use CrazyGoat\Router\Interfaces\CacheProvider;

include '../vendor/autoload.php';

$dispatcher = DispatcherFactory::createFileCached('data/router-file.php', 'cache/router.cache');

var_dump($dispatcher->dispatch('GET', '/users'));
