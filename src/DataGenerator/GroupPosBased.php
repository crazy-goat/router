<?php
declare(strict_types=1);

namespace CrazyGoat\Router\DataGenerator;

final class GroupPosBased extends RegexBasedAbstract
{
    protected function getApproxChunkSize(): int
    {
        return 10;
    }

    protected function processChunk(array $regexToRoutesMap): array
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
