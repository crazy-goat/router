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
    public $middleware;

    /**
     * Constructs a route (value object).
     *
     * @param string $httpMethod
     * @param mixed $handler
     * @param string $regex
     * @param array $variables
     * @param array $middleware
     */
    public function __construct($httpMethod, $handler, $regex, $variables, $middleware = [])
    {
        $this->httpMethod = $httpMethod;
        $this->handler = $handler;
        $this->regex = $regex;
        $this->variables = $variables;
        $this->middleware = $middleware;
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
