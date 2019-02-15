<?php
declare(strict_types=1);

namespace CrazyGoat\Router;

use CrazyGoat\Router\Interfaces\RouteInterface;

final class Route implements RouteInterface
{
    /** @var string */
    public $httpMethod;

    /** @var string */
    public $regex;

    /** @var array */
    public $variables;

    /** @var string */
    public $handler;

    /** @var array */
    public $middleware;

    /** @var ?string */
    public $name;

    /**
     * Constructs a route (value object).
     *
     * @param string $httpMethod
     * @param string $handler
     * @param string $regex
     * @param array $variables
     * @param array $middleware
     * @param string|null $name
     */
    public function __construct(
        string $httpMethod,
        string $handler,
        string $regex,
        array $variables,
        array $middleware = [],
        ?string $name = null
    ) {
        $this->httpMethod = $httpMethod;
        $this->handler = $handler;
        $this->regex = $regex;
        $this->variables = $variables;
        $this->middleware = $middleware;
        $this->name = $name;
    }

    /**
     * Tests whether this route matches the given string.
     *
     * @param string $str
     *
     * @return bool
     */
    public function matches(string $str): bool
    {
        $regex = '~^' . $this->regex . '$~';
        return (bool) preg_match($regex, $str);
    }
}
