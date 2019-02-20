CrazyGoat\Route - Crazy router for PHP, based on FastRoute
=======================================

This library provides a fast implementation of a regular expression based router. [Blog post explaining how the
implementation works and why it is fast.][blog_post]  
This fork add these crazy functions:
 - middleware stack - add some middlewares to your route
 - route generation - for named route you can generate a path 
 - pass max phpstan - possible less bugs :P
 
Install
-------

To install with composer:

```sh
composer require crazy-goat/router
```

Requires PHP 7.1 or newer.

Usage
-----

Here's a basic usage example:

```php
<?php

require '/path/to/vendor/autoload.php';

$routing = function (CrazyGoat\Router\RouteCollector $r) {
    $r->get('/users', 'get_all_users_handler');
    // {id} must be a number (\d+)
    $r->get('/user/{id:\d+}', 'get_user_handler');
    // The /{title} suffix is optional
    $r->get('/articles/{id:\d+}[/{title}]', 'get_article_handler');
};

$dispatcher = CrazyGoat\Router\DispatcherFactory::createFromClosure($routing);

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

try {
    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
    
    $handler = $routeInfo->getHandler();
    $params = $routeInfo->getVariables();
    $middlewareStack = $routeInfo->getMiddlewareStack();
    
    // ... call $handler with $vars
} catch (\CrazyGoat\Router\Exceptions\MethodNotAllowed $exception) {
    // ... 405 Method Not Allowed
} catch (\CrazyGoat\Router\Exceptions\RouteNotFound $exception) {
   // ... 404 Not Found
}
```

### Defining routes

The routes are defined by calling the `CrazyGoat\Router\DispatcherFactory::createFromClosure()` or  function, which accepts
a callable taking a `CrazyGoat\Router\RouteCollector` instance. The routes are added by calling
`addRoute()` on the collector instance:

```php
$r->addRoute([$method], $routePattern, $handler);
```

The `$method` is an uppercase HTTP method string for which a certain route should match. It
is possible to specify multiple valid methods using an array:

```php
// These two calls
$r->addRoute(['GET'], '/test', 'handler');
$r->addRoute(['POST'], '/test', 'handler');
// Are equivalent to this one call
$r->addRoute(['GET', 'POST'], '/test', 'handler');
```

By default the `$routePattern` uses a syntax where `{foo}` specifies a placeholder with name `foo`
and matching the regex `[^/]+`. To adjust the pattern the placeholder matches, you can specify
a custom pattern by writing `{bar:[0-9]+}`. Some examples:

```php
// Matches /user/42, but not /user/xyz
$r->addRoute(['GET'], '/user/{id:\d+}', 'handler');

// Matches /user/foobar, but not /user/foo/bar
$r->addRoute(['GET'], '/user/{name}', 'handler');

// Matches /user/foo/bar as well
$r->addRoute(['GET'], '/user/{name:.+}', 'handler');
```

Custom patterns for route placeholders cannot use capturing groups. For example `{lang:(en|de)}`
is not a valid placeholder, because `()` is a capturing group. Instead you can use either
`{lang:en|de}` or `{lang:(?:en|de)}`.

Furthermore parts of the route enclosed in `[...]` are considered optional, so that `/foo[bar]`
will match both `/foo` and `/foobar`. Optional parts are only supported in a trailing position,
not in the middle of a route.

```php
// This route
$r->addRoute(['GET'], '/user/{id:\d+}[/{name}]', 'handler');
// Is equivalent to these two routes
$r->addRoute(['GET'], '/user/{id:\d+}', 'handler');
$r->addRoute(['GET'], '/user/{id:\d+}/{name}', 'handler');

// Multiple nested optional parts are possible as well
$r->addRoute(['GET'], '/user[/{id:\d+}[/{name}]]', 'handler');

// This route is NOT valid, because optional parts can only occur at the end
$r->addRoute(['GET'], '/user[/{id:\d+}]/{name}', 'handler');
```

The `$handler` parameter does not necessarily have to be a callback, it could also be a controller
class name or any other kind of data you wish to associate with the route. CrazyGoat\Router only tells you
which handler corresponds to your URI, how you interpret it is up to you.

#### Shortcut methods for common request methods

For the `GET`, `POST`, `PUT`, `PATCH`, `DELETE` and `HEAD` request methods shortcut methods are available. For example:

```php
$r->get('/get-route', 'get_handler');
$r->post('/post-route', 'post_handler');
```

Is equivalent to:

```php
$r->addRoute('GET', '/get-route', 'get_handler');
$r->addRoute('POST', '/post-route', 'post_handler');
```

#### Route Groups

Additionally, you can specify routes inside of a group. All routes defined inside a group will have a common prefix.

For example, defining your routes as:

```php
$r->addGroup('/admin', function (RouteCollector $r) {
    $r->addRoute(['GET'], '/do-something', 'handler');
    $r->addRoute(['GET'], '/do-another-thing', 'handler');
    $r->addRoute(['GET'], '/do-something-else', 'handler');
});
```

Will have the same result as:

 ```php
$r->addRoute(['GET'], '/admin/do-something', 'handler');
$r->addRoute(['GET'], '/admin/do-another-thing', 'handler');
$r->addRoute(['GET'], '/admin/do-something-else', 'handler');
 ```

Nested groups are also supported, in which case the prefixes of all the nested groups are combined.

### Caching

By using `cachedDispatcher` instead of `simpleDispatcher` you can cache the generated
routing data and construct the dispatcher from the cached information:

```php
<?php
$dispatcher = DispatcherFactory::createFileCached('data/router-file.php', 'cache/router.cache');
```
First parameter is the file consist routing definitions. This file should return `Closure` with routing definition:

```php
<?php
return function (CrazyGoat\Router\RouteCollector $r) {
    $r->get('/users', 'get_all_users_handler');
    // {id} must be a number (\d+)
    $r->get('/user/{id:\d+}', 'get_user_handler');
    // The /{title} suffix is optional
    $r->get('/articles/{id:\d+}[/{title}]', 'get_article_handler');
};
```

The second parameter is the path for file cache file. If cache file not exists, routing data is loaded from
first parameter. 

### Dispatching a URI

A URI is dispatched by calling the `dispatch()` method of the created dispatcher. This method
accepts the HTTP method and a URI. Getting those two bits of information (and normalizing them
appropriately) is your job - this library is not bound to the PHP web SAPIs.

The `dispatch()` method returns an `RouteInfo` object which contains information about handler, variables and middleware stack. 
If none route match request uri the `RouteNotFound` exceptions is thrown.
If route is found but method does not match request method the `MethodNotAllowed` exceptions will be thrown. You can
get allowed methods from exceptions using calling `getAllowedMethods()` function.

```php
<?php
try {
    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
    // ... your code 
} catch (\CrazyGoat\Router\Exceptions\MethodNotAllowed $exception) {
    $allowedMethods = $exception->getAllowedMethods();
}
```

> **NOTE:** The HTTP specification requires that a `405 Method Not Allowed` response include the
`Allow:` header to detail available methods for the requested resource. Applications using CrazyGoat\Router
should use the array from `getAllowedMethods()` to add this header when relaying a 405 response.

For the found status the `RouteInfo` object contains handler that was associated with the route,
dictionary of placeholder names to their values and the middleware stack. For example:

```php
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
    
$handler = $routeInfo->getHandler();
$params = $routeInfo->getVariables();
$middlewareStack = $routeInfo->getMiddlewareStack();
```

### Overriding the route parser and dispatcher

The routing process makes use of three components: A route parser, a data generator and a
dispatcher. The three components adhere to the following interfaces:

```php
<?php

namespace CrazyGoat\Router;

interface RouteParser {
    public function parse(string $route): array;
}

interface DataGenerator {
    public function addRoute(string $httpMethod, array $routeData, string $handler, array $middleware = [], ?string $name = null): void;
    public function getData(): array;
    public function hasNamedRoute(string $name): bool;
}

interface Dispatcher {
    const NOT_FOUND = 0, FOUND = 1, METHOD_NOT_ALLOWED = 2;

    public function dispatch(string $httpMethod, string $uri): RouteInfo;
    public function setData(array $data): void;
}
```

The route parser takes a route pattern string and converts it into an array of route infos, where
each route info is again an array of it's parts. The structure is best understood using an example:

    /* The route /user/{id:\d+}[/{name}] converts to the following array: */
    [
        [
            '/user/',
            ['id', '\d+'],
        ],
        [
            '/user/',
            ['id', '\d+'],
            '/',
            ['name', '[^/]+'],
        ],
    ]

This array can then be passed to the `addRoute()` method of a data generator. After all routes have
been added the `getData()` of the generator is invoked, which returns all the routing data required
by the dispatcher. The format of this data is not further specified - it is tightly coupled to
the corresponding dispatcher.

The dispatcher accepts the routing data via a constructor or `setData` function and provides a `dispatch()` method, which
you're already familiar with.

The route parser can be overwritten individually (to make use of some different pattern syntax),
however the data generator and dispatcher should always be changed as a pair, as the output from
the former is tightly coupled to the input of the latter. The reason the generator and the
dispatcher are separate is that only the latter is needed when using caching (as the output of
the former is what is being cached.)

To use custom parser, generator or dispatcher create new `Configuration` object and pass it to 
`DispatcherFactory::prepareDispatcher()` function:

```php
<?php

$config = new Configuration(
    new ClosureProvider($routingData),
    new RouteCollector(
        new CustomParser(),
        New CustomDataGenerator()
    ),
    new CustomDispatcher()       
);
```
### Middleware

Adding middleware to route is very simple, just pass `middleware` paramter to `addRoute()` or `addGroup()` method in `RouteCollecotr`.

```php
$dispatcher = DispatcherFactory::createFromClosure(function(CrazyGoat\Router\RouteCollector $r) {
    $r->addRoute(['GET'], '/users', 'get_all_users_handler', ['root_middleware']);
    $r->addGroup('/nested', function (CrazyGoat\Router\RouteCollector $r) {
        $r->addRoute(['GET'], '/users', 'handler3', ['nested-middleware']);
    }, ['group_middleware']);
});
```

For the first route `/users` only `root_middleware` will be returned. For nested routes like `/nested/users` both middleware 
`group_middleware` and  `nested-middleware` will be returned. You can also add more than one middleware to route:

```php
    $r->addRoute(['GET'], '/users', 'get_all_users_handler', ['first', 'second']);
```

Middleware stack is returned in `routeInfo` third index. If no middlewares where added to route an empty array will be returned.

```php
$r->addRoute(['GET'], '/users', 'get_all_users_handler', ['first', 'second']);

//some usefull code

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
$middlewares = $routeInfo->getMiddlewareStack();
``` 

### Named routes and path generation

CrazyRoute provide an easy way to generate path for named route. First we must add a route with name. Name is passed as 
fifth parameter in `addRoute()` function. Route name must be unique else an exception `BadRouteException` will be thrown. 
Now all we have to do is call `produce()` function on `Dispatcher` object. 

```php
$r->addRoute(['GET'], '/users', 'get_all_users_handler', [], 'users');

// some crazy code

$path = $dispatcher->produce('users', $route_params);
```

All required route params must be passed in second argument otherwise an exception will be thrown. 

### A Note on HEAD Requests

The HTTP spec requires servers to [support both GET and HEAD methods][2616-511]:

> The methods GET and HEAD MUST be supported by all general-purpose servers

To avoid forcing users to manually register HEAD routes for each resource we fallback to matching an
available GET route for a given resource. The PHP web SAPI transparently removes the entity body
from HEAD responses so this behavior has no effect on the vast majority of users.

However, implementers using CrazyGoat\Router outside the web SAPI environment (e.g. a custom server) MUST
NOT send entity bodies generated in response to HEAD requests. If you are a non-SAPI user this is
*your responsibility*; CrazyGoat\Router has no purview to prevent you from breaking HTTP in such cases.

Finally, note that applications MAY always specify their own HEAD method route for a given
resource to bypass this behavior entirely.

### Credits
This library is based on a FastRoute developed by [Nikita Popov][nikic].

A large number of tests, as well as HTTP compliance considerations, were provided by [Daniel Lowrey][rdlowrey].


[2616-511]: http://www.w3.org/Protocols/rfc2616/rfc2616-sec5.html#sec5.1.1 "RFC 2616 Section 5.1.1"
[blog_post]: http://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html
[nikic]: https://github.com/nikic/FastRoute
[rdlowrey]: https://github.com/rdlowrey

