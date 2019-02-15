<?php
return function(CrazyGoat\Router\RouteCollector $r) {
    $r->get('/users', 'get_all_users_handler');
    // {id} must be a number (\d+)
    $r->get('/user/{id:\d+}', 'get_user_handler');
    // The /{title} suffix is optional
    $r->get('/articles/{id:\d+}[/{title}]', 'get_article_handler');
};