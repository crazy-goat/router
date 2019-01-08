<?php

namespace FastRoute\Dispatcher;

class GroupCountBased extends RegexBasedAbstract
{
    public function __construct($data)
    {
        list($this->staticRouteMap, $this->variableRouteData, $this->namedRoutes) = $data;
    }

    protected function dispatchVariableRoute($routeData, $uri)
    {
        foreach ($routeData as $data) {
            if (!preg_match($data['regex'], $uri, $matches)) {
                continue;
            }

            list($handler, $varNames, $middlewares) = $data['routeMap'][count($matches)];

            $vars = [];
            $i = 0;
            foreach ($varNames as $varName) {
                $vars[$varName] = $matches[++$i];
            }
            return [self::FOUND, $handler, $vars, $middlewares];
        }

        return [self::NOT_FOUND];
    }
}
