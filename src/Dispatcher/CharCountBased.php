<?php
declare(strict_types=1);

namespace CrazyGoat\Router\Dispatcher;

final class CharCountBased extends RegexBasedAbstract
{
    public function __construct(?array $data = null)
    {
        list($this->staticRouteMap, $this->variableRouteData, $this->namedRoutes) = $data ?? [[],[],[]];
    }

    protected function dispatchVariableRoute(array $routeData, string $uri): array
    {
        foreach ($routeData as $data) {
            if (!preg_match($data['regex'], $uri . $data['suffix'], $matches)) {
                continue;
            }

            list($handler, $varNames, $middlewares) = $data['routeMap'][end($matches)];

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
