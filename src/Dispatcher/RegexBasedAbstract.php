<?php
declare(strict_types=1);

namespace CrazyGoat\Router\Dispatcher;

use CrazyGoat\Router\Exceptions\BadRouteException;
use CrazyGoat\Router\Exceptions\MethodNotAllowed;
use CrazyGoat\Router\Exceptions\RouteNotFound;
use CrazyGoat\Router\Interfaces\Dispatcher;
use CrazyGoat\Router\Interfaces\RouteGenerator;
use CrazyGoat\Router\RouteInfo;

abstract class RegexBasedAbstract implements Dispatcher, RouteGenerator
{
    /** @var mixed[][] */
    protected $staticRouteMap = [];

    /** @var mixed[] */
    protected $variableRouteData = [];

    /** @var array */
    protected $namedRoutes = [];

    /**
     * @param array $routeData
     * @param string $uri
     * @return mixed[]
     */
    abstract protected function dispatchVariableRoute(array $routeData, string $uri): array;

    public function setData(array $data): void
    {
        list($this->staticRouteMap, $this->variableRouteData, $this->namedRoutes) = $data;
    }

    /**
     * @param string $httpMethod
     * @param string $uri
     * @return RouteInfo
     * @throws MethodNotAllowed
     * @throws RouteNotFound
     */
    public function dispatch(string $httpMethod, string $uri): RouteInfo
    {
        if (isset($this->staticRouteMap[$httpMethod][$uri])) {
            list($handler, $middleware) = $this->staticRouteMap[$httpMethod][$uri];
            return new RouteInfo([self::FOUND, $handler, [], $middleware]);
        }

        $varRouteData = $this->variableRouteData;
        if (isset($varRouteData[$httpMethod])) {
            $result = $this->dispatchVariableRoute($varRouteData[$httpMethod], $uri);
            if ($result[0] === self::FOUND) {
                return new RouteInfo($result);
            }
        }

        // For HEAD requests, attempt fallback to GET
        if ($httpMethod === 'HEAD') {
            if (isset($this->staticRouteMap['GET'][$uri])) {
                list($handler, $middleware) = $this->staticRouteMap['GET'][$uri];
                return new RouteInfo([self::FOUND, $handler, [], $middleware]);
            }
            if (isset($varRouteData['GET'])) {
                $result = $this->dispatchVariableRoute($varRouteData['GET'], $uri);
                if ($result[0] === self::FOUND) {
                    return new RouteInfo($result);
                }
            }
        }

        // If nothing else matches, try fallback routes
        if (isset($this->staticRouteMap['*'][$uri])) {
            list($handler, $middleware) = $this->staticRouteMap['*'][$uri];
            return new RouteInfo([self::FOUND, $handler, [], $middleware]);
        }
        if (isset($varRouteData['*'])) {
            $result = $this->dispatchVariableRoute($varRouteData['*'], $uri);
            if ($result[0] === self::FOUND) {
                return new RouteInfo($result);
            }
        }

        // Find allowed methods for this URI by matching against all other HTTP methods as well
        $allowedMethods = [];

        foreach ($this->staticRouteMap as $method => $uriMap) {
            if ($method !== $httpMethod && isset($uriMap[$uri])) {
                $allowedMethods[] = $method;
            }
        }

        foreach ($varRouteData as $method => $routeData) {
            if ($method === $httpMethod) {
                continue;
            }

            $result = $this->dispatchVariableRoute($routeData, $uri);
            if ($result[0] === self::FOUND) {
                $allowedMethods[] = $method;
            }
        }

        // If there are no allowed methods the route simply does not exist
        if ($allowedMethods) {
            throw new MethodNotAllowed($httpMethod, $allowedMethods);
        }

        throw new RouteNotFound();
    }

    /**
     * @param string $name
     * @param array $params
     * @return string
     * @throws \Exception
     */
    public function pathFor(string $name, array $params = []): string
    {
        if (isset($this->namedRoutes[$name])) {
            $route = $this->namedRoutes[$name];
            if (is_array($route)) {
                $lastException = null;
                foreach (array_reverse($route) as $routeOption) {
                    try {
                        return $this->produceVariable($routeOption, $params);
                    } catch (BadRouteException $exception) {
                        $lastException = $exception;
                    }
                }

                if ($lastException) {
                    throw $lastException;
                }
            } elseif (is_string($route)) {
                return $route;
            }
        }

        throw new BadRouteException('No route found with name:'.$name);
    }

    /**
     * @param array $route
     * @param array $params
     * @return string
     */
    private function produceVariable(array $route, array $params): string
    {
        $path = [];

        foreach ($route as $segment) {
            if (is_string($segment)) {
                $path[] = $segment;
            } elseif (is_array($segment)) {
                if (array_key_exists($segment[0], $params)) {
                    $path[] = $params[$segment[0]];
                } else {
                    throw new BadRouteException('Missing route parameter "'.$segment[0].'"');
                }
            }
        }
        return implode('', $path);
    }
}
