<?php

namespace FastRoute;

class RouteCollector
{
    /** @var RouteParser */
    protected $routeParser;

    /** @var DataGenerator */
    protected $dataGenerator;

    /** @var string */
    protected $currentGroupPrefix;

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
     * @param string|string[] $httpMethod
     * @param string $route
     * @param mixed $handler
     * @param array $middleware
     * @param string|null $name
     */
    public function addRoute($httpMethod, $route, $handler, $middleware = [], $name = null)
    {
        $route = $this->currentGroupPrefix . $route;

        if (!empty($this->currentMiddleware)) {
            array_unshift($middleware, ...$this->currentMiddleware);
        }

        $routeDatas = $this->routeParser->parse($route);
        foreach ((array) $httpMethod as $method) {
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
     * @param callable $callback
     * @param array $middleware
     */
    public function addGroup($prefix, callable $callback, $middleware = [])
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
     * @param mixed $handler
     * @param array $middleware
     */
    public function get($route, $handler, $middleware = [])
    {
        $this->addRoute('GET', $route, $handler, $middleware);
    }

    /**
     * Adds a POST route to the collection
     *
     * This is simply an alias of $this->addRoute('POST', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     * @param array $middleware
     */
    public function post($route, $handler, $middleware = [])
    {
        $this->addRoute('POST', $route, $handler, $middleware);
    }

    /**
     * Adds a PUT route to the collection
     *
     * This is simply an alias of $this->addRoute('PUT', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     * @param array $middleware
     */
    public function put($route, $handler, $middleware = [])
    {
        $this->addRoute('PUT', $route, $handler, $middleware);
    }

    /**
     * Adds a DELETE route to the collection
     *
     * This is simply an alias of $this->addRoute('DELETE', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     * @param array $middleware
     */
    public function delete($route, $handler, $middleware = [])
    {
        $this->addRoute('DELETE', $route, $handler, $middleware);
    }

    /**
     * Adds a PATCH route to the collection
     *
     * This is simply an alias of $this->addRoute('PATCH', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     * @param array $middleware
     */
    public function patch($route, $handler, $middleware = [])
    {
        $this->addRoute('PATCH', $route, $handler, $middleware);
    }

    /**
     * Adds a HEAD route to the collection
     *
     * This is simply an alias of $this->addRoute('HEAD', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     * @param array $middleware
     */
    public function head($route, $handler, $middleware = [])
    {
        $this->addRoute('HEAD', $route, $handler, $middleware);
    }

    /**
     * Adds an OPTIONS route to the collection
     *
     * This is simply an alias of $this->addRoute('OPTIONS', $route, $handler)
     *
     * @param string $route
     * @param mixed $handler
     * @param array $middleware
     */
    public function options($route, $handler, $middleware = [])
    {
        $this->addRoute('OPTIONS', $route, $handler, $middleware);
    }

    /**
     * Returns the collected route data, as provided by the data generator.
     *
     * @return array
     */
    public function getData()
    {
        return $this->dataGenerator->getData();
    }
}
