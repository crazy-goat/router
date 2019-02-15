<?php
declare(strict_types=1);

namespace CrazyGoat\Router;

class RouteCollector
{
    /** @var RouteParser */
    protected $routeParser;

    /** @var DataGenerator */
    protected $dataGenerator;

    /** @var string */
    protected $currentGroupPrefix;

    /** @var array */
    protected $currentMiddleware = [];

    /**
     * Constructs a route collector.
     *
     * @param RouteParser   $routeParser
     * @param DataGenerator $dataGenerator
     */
    public function __construct(RouteParser $routeParser, DataGenerator $dataGenerator)
    {
        $this->routeParser = $routeParser;
        $this->dataGenerator = $dataGenerator;
        $this->currentGroupPrefix = '';
        $this->currentMiddleware = [];
    }

    /**
     * Adds a route to the collection.
     *
     * The syntax used in the $route string depends on the used route parser.
     *
     * @param array $httpMethod
     * @param string $route
     * @param string $handler
     * @param array $middleware
     * @param string|null $name
     */
    public function addRoute(array $httpMethod, string $route, string $handler, array $middleware = [], ?string $name = null): void
    {
        $route = $this->currentGroupPrefix . $route;

        if (!empty($this->currentMiddleware)) {
            array_unshift($middleware, ...$this->currentMiddleware);
        }

        $routeDatas = $this->routeParser->parse($route);
        if (!is_null($name) && $this->dataGenerator->hasNamedRoute($name)) {
            throw new BadRouteException('Named route: "'.$name.'" already exists');
        }

        foreach ($httpMethod as $method) {
            foreach ($routeDatas as $routeData) {
                $this->dataGenerator->addRoute($method, $routeData, $handler, $middleware, $name);
            }
        }
    }

    /**
     * Create a route group with a common prefix.
     *
     * All routes created in the passed callback will have the given group prefix prepended.
     *
     * @param string $prefix
     * @param \Closure $callback
     * @param array $middleware
     */
    public function addGroup(string $prefix, \Closure $callback, array $middleware = []): void
    {
        $previousGroupPrefix = $this->currentGroupPrefix;
        $previousMiddleware = $this->currentMiddleware;
        $this->currentGroupPrefix = $previousGroupPrefix . $prefix;
        if (!empty($middleware)) {
            array_push($this->currentMiddleware, ...$middleware);
        }
        $callback($this);
        $this->currentMiddleware = $previousMiddleware;
        $this->currentGroupPrefix = $previousGroupPrefix;
    }

    /**
     * Adds a GET route to the collection
     *
     * This is simply an alias of $this->addRoute('GET', $route, $handler)
     *
     * @param string $route
     * @param string $handler
     * @param array $middleware
     */
    public function get(string $route, string $handler, array $middleware = []): void
    {
        $this->addRoute(['GET'], $route, $handler, $middleware);
    }

    /**
     * Adds a POST route to the collection
     *
     * This is simply an alias of $this->addRoute('POST', $route, $handler)
     *
     * @param string $route
     * @param string $handler
     * @param array $middleware
     */
    public function post(string $route, string $handler, array $middleware = []): void
    {
        $this->addRoute(['POST'], $route, $handler, $middleware);
    }

    /**
     * Adds a PUT route to the collection
     *
     * This is simply an alias of $this->addRoute('PUT', $route, $handler)
     *
     * @param string $route
     * @param string $handler
     * @param array $middleware
     */
    public function put(string $route, string $handler, array $middleware = []): void
    {
        $this->addRoute(['PUT'], $route, $handler, $middleware);
    }

    /**
     * Adds a DELETE route to the collection
     *
     * This is simply an alias of $this->addRoute('DELETE', $route, $handler)
     *
     * @param string $route
     * @param string $handler
     * @param array $middleware
     */
    public function delete(string $route, string $handler, array $middleware = []): void
    {
        $this->addRoute(['DELETE'], $route, $handler, $middleware);
    }

    /**
     * Adds a PATCH route to the collection
     *
     * This is simply an alias of $this->addRoute('PATCH', $route, $handler)
     *
     * @param string $route
     * @param string $handler
     * @param array $middleware
     */
    public function patch(string $route, string $handler, array $middleware = []): void
    {
        $this->addRoute(['PATCH'], $route, $handler, $middleware);
    }

    /**
     * Adds a HEAD route to the collection
     *
     * This is simply an alias of $this->addRoute('HEAD', $route, $handler)
     *
     * @param string $route
     * @param string $handler
     * @param array $middleware
     */
    public function head(string $route, string $handler, array $middleware = []): void
    {
        $this->addRoute(['HEAD'], $route, $handler, $middleware);
    }

    /**
     * Adds an OPTIONS route to the collection
     *
     * This is simply an alias of $this->addRoute('OPTIONS', $route, $handler)
     *
     * @param string $route
     * @param string $handler
     * @param array $middleware
     */
    public function options(string $route, string $handler, array $middleware = []): void
    {
        $this->addRoute(['OPTIONS'], $route, $handler, $middleware);
    }

    /**
     * Adds an ANY route to the collection
     *
     * This is simply an alias of $this->addRoute('*', $route, $handler)
     *
     * @param string $route
     * @param string $handler
     * @param array $middleware
     */
    public function any(string $route, string $handler, array $middleware = []): void
    {
        $this->addRoute(['*'], $route, $handler, $middleware);
    }

    /**
     * Returns the collected route data, as provided by the data generator.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->dataGenerator->getData();
    }
}
