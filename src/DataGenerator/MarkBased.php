<?php
declare(strict_types=1);

namespace CrazyGoat\Router\DataGenerator;

use CrazyGoat\Router\Route;

final class MarkBased extends RegexBasedAbstract
{
    protected function getApproxChunkSize(): int
    {
        return 30;
    }

    protected function processChunk(array $regexToRoutesMap): array
    {
        $routeMap = [];
        $regexes = [];
        $markName = 'a';

        /**
         * @var string $regex
         * @var Route $route
         */
        foreach ($regexToRoutesMap as $regex => $route) {
            $regexes[] = $regex . '(*MARK:' . $markName . ')';
            $routeMap[$markName] = [$route->handler, $route->variables, $route->middleware];
            $markName = chr(ord($markName) + 1);
        }

        $regex = '~^(?|' . implode('|', $regexes) . ')$~';
        return ['regex' => $regex, 'routeMap' => $routeMap];
    }
}
