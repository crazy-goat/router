<?php

namespace FastRoute\DataGenerator;

use FastRoute\DataGenerator;
use FastRoute\BadRouteException;
use FastRoute\Route;

abstract class RegexBasedAbstract implements DataGenerator {
    protected $staticRoutes = [];
    protected $regexToRoutesMap = [];

    public function addRoute($httpMethod, $routeData, $handler) {
        if ($this->isStaticRoute($routeData)) {
            $this->addStaticRoute($httpMethod, $routeData, $handler);
        } else {
            $this->addVariableRoute($httpMethod, $routeData, $handler);
        }
    }

    private function isStaticRoute($routeData) {
        return count($routeData) == 1 && is_string($routeData[0]);
    }

    private function addStaticRoute($httpMethod, $routeData, $handler) {
        $routeStr = $routeData[0];

        if (isset($this->staticRoutes[$routeStr][$httpMethod])) {
            throw new BadRouteException(sprintf(
                'Cannot register two routes matching "%s" for method "%s"',
                $routeStr, $httpMethod
            ));
        }

        foreach ($this->regexToRoutesMap as $routes) {
            if (!isset($routes[$httpMethod])) continue;

            $route = $routes[$httpMethod];
            if ($route->matches($routeStr)) {
                throw new BadRouteException(sprintf(
                    'Static route "%s" is shadowed by previously defined variable route "%s" for method "%s"',
                    $routeStr, $route->regex, $httpMethod
                ));
            }
        }

        $this->staticRoutes[$routeStr][$httpMethod] = $handler;
    }

    private function addVariableRoute($httpMethod, $routeData, $handler) {
        list($regex, $variables) = $this->buildRegexForRoute($routeData);

        if (isset($this->regexToRoutesMap[$regex][$httpMethod])) {
            throw new BadRouteException(sprintf(
                'Cannot register two routes matching "%s" for method "%s"',
                $regex, $httpMethod
            ));
        }

        $this->regexToRoutesMap[$regex][$httpMethod] = new Route(
            $httpMethod, $handler, $regex, $variables
        );
    }

    private function buildRegexForRoute($routeData) {
        $regex = '';
        $variables = [];
        foreach ($routeData as $part) {
            if (is_string($part)) {
                $regex .= preg_quote($part, '~');
                continue;
            }

            list($varName, $regexPart) = $part;

            if (isset($variables[$varName])) {
                throw new BadRouteException(sprintf(
                    'Cannot use the same placeholder "%s" twice', $varName
                ));
            }

            $variables[$varName] = $varName;
            $regex .= '(' . $regexPart . ')';
        }

        return [$regex, $variables];
    }
}