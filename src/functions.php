<?php
declare(strict_types=1);

namespace CrazyGoat\Router;

if (!function_exists('CrazyGoat\Router\simpleDispatcher')) {
    /**
     * @param \Closure $routeDefinitionCallback
     * @param array $options
     *
     * @return Dispatcher
     */
    function simpleDispatcher(\Closure $routeDefinitionCallback, array $options = []): Dispatcher
    {
        $options += [
            'routeParser' => 'CrazyGoat\\Router\\RouteParser\\Std',
            'dataGenerator' => 'CrazyGoat\\Router\\DataGenerator\\GroupCountBased',
            'dispatcher' => 'CrazyGoat\\Router\\Dispatcher\\GroupCountBased',
            'routeCollector' => 'CrazyGoat\\Router\\RouteCollector',
        ];

        /** @var RouteCollector $routeCollector */
        $routeCollector = new $options['routeCollector'](
            new $options['routeParser'], new $options['dataGenerator']
        );
        $routeDefinitionCallback($routeCollector);

        return new $options['dispatcher']($routeCollector->getData());
    }

    /**
     * @param \Closure $routeDefinitionCallback
     * @param array $options
     *
     * @return Dispatcher
     */
    function cachedDispatcher(\Closure $routeDefinitionCallback, array $options = []): Dispatcher
    {
        $options += [
            'routeParser' => 'CrazyGoat\\Router\\RouteParser\\Std',
            'dataGenerator' => 'CrazyGoat\\Router\\DataGenerator\\GroupCountBased',
            'dispatcher' => 'CrazyGoat\\Router\\Dispatcher\\GroupCountBased',
            'routeCollector' => 'CrazyGoat\\Router\\RouteCollector',
            'cacheDisabled' => false,
        ];

        if (!isset($options['cacheFile'])) {
            throw new \LogicException('Must specify "cacheFile" option');
        }

        if (!$options['cacheDisabled'] && file_exists($options['cacheFile'])) {
            $dispatchData = require $options['cacheFile'];
            if (!is_array($dispatchData)) {
                throw new \RuntimeException('Invalid cache file "' . $options['cacheFile'] . '"');
            }
            return new $options['dispatcher']($dispatchData);
        }

        $routeCollector = new $options['routeCollector'](
            new $options['routeParser'], new $options['dataGenerator']
        );
        $routeDefinitionCallback($routeCollector);

        /** @var RouteCollector $routeCollector */
        $dispatchData = $routeCollector->getData();
        if (!$options['cacheDisabled']) {
            file_put_contents(
                $options['cacheFile'],
                '<?php return ' . var_export($dispatchData, true) . ';'
            );
        }

        return new $options['dispatcher']($dispatchData);
    }
}
