<?php
declare(strict_types=1);

use CrazyGoat\Router\DispatcherFactory;

include '../vendor/autoload.php';

$routing = function (CrazyGoat\Router\RouteCollector $r) {
    $r->get('/users', 'get_all_users_handler');
    // {id} must be a number (\d+)
    $r->get('/user/{id:\d+}', 'get_user_handler');
    // The /{title} suffix is optional
    $r->get('/articles/{id:\d+}[/{title}]', 'get_article_handler');
};

$dispatcher = DispatcherFactory::createFromClosure($routing);

var_dump($dispatcher->dispatch('GET', '/users'));
