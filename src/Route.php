<?php

namespace FastRoute;

class Route
{
    /** @var string */
    public $httpMethod;

    /** @var string */
    public $regex;

    /** @var array */
    public $variables;

    /** @var mixed */
    public $handler;

    /**
     * @var array
     */
    public $middleware;

    /**
     * @var string|null
     */
    public $name;

    /**
     * Constructs a route (value object).
     *
     * @param string $httpMethod
     * @param mixed $handler
     * @param string $regex
     * @param array $variables
     * @param array $middleware
     */
    public function __construct($httpMethod, $handler, $regex, $variables, $middleware = [], $name = null)
    {
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
    public function matches($str)
    {
        $regex = '~^' . $this->regex . '$~';
        return (bool) preg_match($regex, $str);
    }
}
