<?php

namespace CrazyGoat\Router;

interface DataGenerator
{
    /**
     * Adds a route to the data generator. The route data uses the
     * same format that is returned by RouterParser::parser().
     *
     * The handler doesn't necessarily need to be a callable, it
     * can be arbitrary data that will be returned when the route
     * matches.
     *
     * @param string $httpMethod
     * @param array $routeData
     * @param string $handler
     * @param array $middleware
     * @param string|null $name
     */
    public function addRoute(
        string $httpMethod,
        array $routeData,
        string $handler,
        array $middleware = [],
        ?string $name = null
    ): void;

    /**
     * Returns dispatcher data in some unspecified format, which
     * depends on the used method of dispatch.
     * @return array
     */
    public function getData(): array;

    /**
     * @param $name string
     * @return bool
     */
    public function hasNamedRoute(string $name): bool;
}
