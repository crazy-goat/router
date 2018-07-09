<?php
require 'vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', 'get_all_users_handler', ['abc']);
    $r->addRoute('GET', '/users', 'get_all_users_handler');
    // {id} must be a number (\d+)
    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler',['cde']);
    // The /{title} suffix is optional
    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
    $r->addGroup('/admin', function (FastRoute\RouteCollector $r) {
        $r->addGroup('/admin', function (FastRoute\RouteCollector $r) {
            $r->addRoute('GET', '/do-something/{id:\d+}', 'handler', ['abc','cde']);
        },['!@#']);
        $r->addRoute('GET', '/do-something', 'handler', ['abc','cde']);
        $r->addRoute('GET', '/do-another-thing', 'handler');
        $r->addRoute('GET', '/do-something-else', 'handler');
    }, ['123','456']);
});

// Fetch method and URI from somewhere
$httpMethod = 'GET';
$uri = '/admin/admin/do-something/123';

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
var_dump($routeInfo);