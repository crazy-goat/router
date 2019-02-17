<?php
declare(strict_types=1);

namespace CrazyGoat\Router\DataGenerator;

use CrazyGoat\Router\Exceptions\BadRouteException;
use CrazyGoat\Router\Interfaces\DataGenerator;
use CrazyGoat\Router\Route;

abstract class RegexBasedAbstract implements DataGenerator
{
    /** @var array[][] */
    protected $staticRoutes = [];

    /** @var Route[][] */
    protected $methodToRegexToRoutesMap = [];

    /** @var array */
    protected $namedRoutes = [];

    /**
     * @return int
     */
    abstract protected function getApproxChunkSize(): int;

    /**
     * @param array $regexToRoutesMap
     * @return array
     */
    abstract protected function processChunk(array $regexToRoutesMap): array;

    public function addRoute(string $httpMethod, array $routeData, string $handler, array $middleware = [], ?string $name = null): void
    {
        if ($this->isStaticRoute($routeData)) {
            $this->addStaticRoute($httpMethod, $routeData, $handler, $middleware, $name);
        } else {
            $this->addVariableRoute($httpMethod, $routeData, $handler, $middleware, $name);
        }
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        if (empty($this->methodToRegexToRoutesMap)) {
            return [$this->staticRoutes, [], $this->namedRoutes];
        }

        return [$this->staticRoutes, $this->generateVariableRouteData(), $this->namedRoutes];
    }

    /**
     * @return array
     */
    private function generateVariableRouteData(): array
    {
        $data = [];
        foreach ($this->methodToRegexToRoutesMap as $method => $regexToRoutesMap) {
            $chunkSize = $this->computeChunkSize(count($regexToRoutesMap));
            $chunks = array_chunk($regexToRoutesMap, $chunkSize, true);
            $data[$method] = array_map([$this, 'processChunk'], $chunks);
        }
        return $data;
    }

    /**
     * @param int $count
     * @return int
     */
    private function computeChunkSize(int $count): int
    {
        $numParts = max(1, round($count / $this->getApproxChunkSize()));
        return (int)ceil($count / $numParts);
    }

    /**
     * @param array $routeData
     * @return bool
     */
    private function isStaticRoute(array $routeData): bool
    {
        return count($routeData) === 1 && is_string($routeData[0]);
    }

    private function addStaticRoute(string $httpMethod, array $routeData, string $handler, array $middlewares = [], ?string $name = null): void
    {
        /** @var string $routeStr */
        $routeStr = $routeData[0];

        if (isset($this->staticRoutes[$httpMethod][$routeStr])) {
            throw new BadRouteException(sprintf(
                'Cannot register two routes matching "%s" for method "%s"',
                $routeStr,
                $httpMethod
            ));
        }

        if (isset($this->methodToRegexToRoutesMap[$httpMethod])) {
            /** @var Route $route */
            foreach ($this->methodToRegexToRoutesMap[$httpMethod] as $route) {
                if ($route->matches($routeStr)) {
                    throw new BadRouteException(sprintf(
                        'Static route "%s" is shadowed by previously defined variable route "%s" for method "%s"',
                        $routeStr,
                        $route->regex,
                        $httpMethod
                    ));
                }
            }
        }

        $this->staticRoutes[$httpMethod][$routeStr] = [$handler, $middlewares];

        if (!empty($name)) {
            $this->namedRoutes[$name] = $routeStr;
        }
    }

    /**
     * @param string $httpMethod
     * @param array $routeData
     * @param string $handler
     * @param array $middlewares
     * @param string|null $name
     *
     */
    private function addVariableRoute(
        string $httpMethod,
        array $routeData,
        string $handler,
        array $middlewares = [],
        ?string $name = null
    ): void {
        /**
         * @var string $regex
         * @var array $variables
         */
        list($regex, $variables) = $this->buildRegexForRoute($routeData);

        if (isset($this->methodToRegexToRoutesMap[$httpMethod][$regex])) {
            throw new BadRouteException(sprintf(
                'Cannot register two routes matching "%s" for method "%s"',
                $regex,
                $httpMethod
            ));
        }
        $route = new Route($httpMethod, $handler, $regex, $variables, $middlewares, $name);

        $this->methodToRegexToRoutesMap[$httpMethod][$regex] = $route;

        if ($name != null) {
            if (!isset($this->namedRoutes[$name])) {
                $this->namedRoutes[$name] = [];
            }
            $this->namedRoutes[$name][] = $routeData;
        }
    }

    /**
     * @param array $routeData
     * @return array
     */
    private function buildRegexForRoute(array $routeData): array
    {
        $regex = '';
        $variables = [];
        /** @var string|array $part */
        foreach ($routeData as $part) {
            if (is_string($part)) {
                $regex .= preg_quote($part, '~');
                continue;
            }

            /**
             * @var string $varName
             * @var string $regexPart
             */
            list($varName, $regexPart) = $part;

            if (isset($variables[$varName])) {
                throw new BadRouteException(sprintf(
                    'Cannot use the same placeholder "%s" twice',
                    $varName
                ));
            }

            if ($this->regexHasCapturingGroups($regexPart)) {
                throw new BadRouteException(sprintf(
                    'Regex "%s" for parameter "%s" contains a capturing group',
                    $regexPart,
                    $varName
                ));
            }

            $variables[$varName] = $varName;
            $regex .= '(' . $regexPart . ')';
        }

        return [$regex, $variables];
    }

    /**
     * @param string $regex
     * @return bool
     */
    private function regexHasCapturingGroups(string $regex): bool
    {
        if (false === strpos($regex, '(')) {
            // Needs to have at least a ( to contain a capturing group
            return false;
        }

        // Semi-accurate detection for capturing groups
        return (bool)preg_match(
            '~
                (?:
                    \(\?\(
                  | \[ [^\]\\\\]* (?: \\\\ . [^\]\\\\]* )* \]
                  | \\\\ .
                ) (*SKIP)(*FAIL) |
                \(
                (?!
                    \? (?! <(?![!=]) | P< | \' )
                  | \*
                )
            ~x',
            $regex
        );
    }

    public function hasNamedRoute(string $name): bool
    {
        return isset($this->namedRoutes[$name]);
    }
}
