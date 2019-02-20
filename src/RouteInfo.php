<?php
declare(strict_types=1);

namespace CrazyGoat\Router;

final class RouteInfo
{
    /** @var string */
    private $handler;

    /** @var array */
    private $variables;

    /** @var array */
    private $middlewareStack;

    public function __construct(array $routeInfo)
    {
        $this->handler = $routeInfo[1];
        $this->variables = $routeInfo[2];
        $this->middlewareStack = $routeInfo[3];
    }

    /**
     * @return string
     */
    public function getHandler(): string
    {
        return $this->handler;
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @return array
     */
    public function getMiddlewareStack(): array
    {
        return $this->middlewareStack;
    }
}