<?php
declare(strict_types=1);

namespace CrazyGoat\Router;

use CrazyGoat\Router\DataGenerator\GroupCountBased as GroupCountCollector;
use CrazyGoat\Router\Dispatcher\GroupCountBased as GroupCountDispatcher;
use CrazyGoat\Router\Exceptions\CacheLoadException;
use CrazyGoat\Router\Exceptions\CacheNotFoundException;
use CrazyGoat\Router\Interfaces\CacheProvider;
use CrazyGoat\Router\Interfaces\Dispatcher;
use CrazyGoat\Router\Provider\ClosureProvider;
use CrazyGoat\Router\Provider\FileCachedProvider;
use CrazyGoat\Router\RouteParser\Std;

final class DispatcherFactory
{
    public static function createFromClosure(\Closure $routing, array $options = []): Dispatcher
    {
        return static::prepareDispatcher(
            new Configuration(
                new ClosureProvider($routing),
                static::defaultCollector($options),
                $options['dispatcher'] ?? static::defaultDispatcher()
            )
        );
    }

    public static function createFileCached(string $routingFile, string $cacheFile, array $options = []): Dispatcher
    {
        return static::prepareDispatcher(
            new Configuration(
                new FileCachedProvider($routingFile, $cacheFile),
                static::defaultCollector($options),
                $options['dispatcher'] ?? static::defaultDispatcher()
            )
        );
    }

    /**
     * @param Configuration $config
     * @return Dispatcher
     */
    public static function prepareDispatcher(Configuration $config): Dispatcher
    {
        $data = null;
        $cacheProvider = $config->getRoutingProvider();
        if ($cacheProvider instanceof CacheProvider) {
            try {
                $data = $cacheProvider->load();
            } catch (CacheLoadException | CacheNotFoundException $exception) {
                $data = null;
            }
        }

        if ($data === null) {
            $collector = $config->getCollector();
            $routes = $cacheProvider->getRouting();
            $routes($collector);
            $data = $collector->getData();
        }

        $dispatcher = $config->getDispatcher();
        $dispatcher->setData($data);

        if ($cacheProvider instanceof CacheProvider) {
            $cacheProvider->save($data);
        }

        return $dispatcher;
    }

    /**
     * @param array $options
     * @return RouteCollector
     */
    private static function defaultCollector(array $options = []): RouteCollector
    {
        return new RouteCollector(
            $options['parser'] ?? new Std(),
            $options['generator'] ?? new GroupCountCollector()
        );
    }

    private static function defaultDispatcher(): Dispatcher
    {
        return new GroupCountDispatcher();
    }
}
