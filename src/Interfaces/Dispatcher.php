<?php
declare(strict_types=1);

namespace CrazyGoat\Router\Interfaces;

use CrazyGoat\Router\Exceptions\MethodNotAllowed;
use CrazyGoat\Router\Exceptions\RouteNotFound;
use CrazyGoat\Router\RouteInfo;

interface Dispatcher
{
    const NOT_FOUND = 0;
    const FOUND = 1;
    const METHOD_NOT_ALLOWED = 2;

    /**
     * Dispatches against the provided HTTP method verb and URI.
     *
     * Returns array with one of the following formats:
     *
     *     [self::NOT_FOUND]
     *     [self::METHOD_NOT_ALLOWED, ['GET', 'OTHER_ALLOWED_METHODS']]
     *     [self::FOUND, $handler, ['varName' => 'value', ...]]
     *
     * @param string $httpMethod
     * @param string $uri
     *
     * @throws RouteNotFound
     * @throws MethodNotAllowed
     *
     * @return RouteInfo
     */
    public function dispatch(string $httpMethod, string $uri): RouteInfo;

    public function setData(array $data): void;
}
