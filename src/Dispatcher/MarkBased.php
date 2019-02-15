<?php
declare(strict_types=1);

namespace CrazyGoat\Router\Dispatcher;

final class MarkBased extends RegexBasedAbstract
{
    public function __construct(?array $data = null)
    {
        list($this->staticRouteMap, $this->variableRouteData, $this->namedRoutes) = $data ?? [[],[],[]];
    }

    protected function dispatchVariableRoute(array $routeData, string $uri): array
    {
        foreach ($routeData as $data) {
            if (!preg_match($data['regex'], $uri, $matches)) {
                continue;
            }

            list($handler, $varNames, $middlewares) = $data['routeMap'][$matches['MARK']];

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
