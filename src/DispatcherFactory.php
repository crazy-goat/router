<?php
declare(strict_types=1);

namespace CrazyGoat\Router;

use CrazyGoat\Router\DataGenerator\GroupCountBased as GroupCountCollector;
use CrazyGoat\Router\Dispatcher\GroupCountBased as GroupCountDispatcher;
use CrazyGoat\Router\Exceptions\CacheLoadException;
use CrazyGoat\Router\Exceptions\RouterFileReadException;
use CrazyGoat\Router\Interfaces\CacheProvider;
use CrazyGoat\Router\Interfaces\Dispatcher;
use CrazyGoat\Router\RouteParser\Std;

final class DispatcherFactory
{
    /**
     * @return RouteCollector
     */
    static public function defaultCollector(): RouteCollector
    {
        return new RouteCollector(
            new Std(),
            new GroupCountCollector()
        );
    }

    static public function defaultDispatcher(): Dispatcher
    {
        return new GroupCountDispatcher();
    }

    /**
     * @param Configuration $config
     * @return Dispatcher
     */
    static public function prepareDispatcher(Configuration $config): Dispatcher
    {
        $data = null;
        if ($config->isCacheEnabled() && $config->getCacheProvider() instanceof CacheProvider) {
            try {
                $data = $config->getCacheProvider()->load();
            } catch (CacheLoadException $exception) {
                $data = static::loadFromFile($config);
            }
        } else {
            $data = static::loadFromFile($config);
        }

        if ($config->isCacheEnabled() && $config->getCacheProvider() instanceof \Closure) {
            $config->getCacheProvider()->save($data);
        }

        $dispatcher = $config->getDispatcher();
        $dispatcher->setData($data);

        return $dispatcher;
    }

    static private function loadFromFile(Configuration $config): array
    {
        $filename = $config->getRouterFile();
        if (file_exists($filename) && is_readable($filename)) {
            $collector = $config->getCollector();
            $routes = include $filename;
            $routes($collector);
            return $collector->getData();
        }
        throw new RouterFileReadException('Failed to load router file: '.$filename);
    }
}