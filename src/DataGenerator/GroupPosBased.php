<?php

namespace CrazyGoat\Router\DataGenerator;

class GroupPosBased extends RegexBasedAbstract
{
    protected function getApproxChunkSize()
    {
        return 10;
    }

    protected function processChunk($regexToRoutesMap)
    {
        $routeMap = [];
        $regexes = [];
        $offset = 1;
        foreach ($regexToRoutesMap as $regex => $route) {
            $regexes[] = $regex;
            $routeMap[$offset] = [$route->handler, $route->variables,  $route->middleware];

            $offset += count($route->variables);
        }

        $regex = '~^(?:' . implode('|', $regexes) . ')$~';
        return ['regex' => $regex, 'routeMap' => $routeMap];
    }
}
