<?php

namespace FastRoute\Dispatcher;

class GroupPosBased extends RegexBasedAbstract
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

            // find first non-empty match
            for ($i = 1; '' === $matches[$i]; ++$i);

            list($handler, $varNames, $middlewares) = $data['routeMap'][$i];

            $vars = [];
            foreach ($varNames as $varName) {
                $vars[$varName] = $matches[$i++];
            }
            return [self::FOUND, $handler, $vars, $middlewares];
        }

        return [self::NOT_FOUND];
    }
}
