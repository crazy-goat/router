<?php
declare(strict_types=1);

namespace CrazyGoat\Router;

use CrazyGoat\Router\Interfaces\CacheProviderInterface;

class Configuration
{
    /**
     * @var RouteCollector
     */
    private $collector;
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var string
     */
    private $routerFile;
    /**
     * @var CacheProviderInterface|null
     */
    private $cacheProvider;

    /**
     * Configuration constructor.
     * @param string $routerFile
     * @param RouteCollector $collector
     * @param Dispatcher $dispatcher
     * @param CacheProviderInterface|null $cacheProvider
     */
    public function __construct(
        string $routerFile,
        RouteCollector $collector,
        Dispatcher $dispatcher,
        ?CacheProviderInterface $cacheProvider = null
    ) {
        $this->routerFile = $routerFile;
        $this->collector = $collector;
        $this->dispatcher = $dispatcher;
        $this->cacheProvider = $cacheProvider;
    }

    public function getCacheProvider(): ?CacheProviderInterface
    {
        return $this->cacheProvider;
    }

    /**
     * @return string
     */
    public function getRouterFile(): string
    {
        return $this->routerFile;
    }

    public function isCacheEnabled(): bool
    {
        return $this->cacheProvider instanceof CacheProviderInterface;
    }

    /**
     * @return RouteCollector
     */
    public function getCollector(): RouteCollector
    {
        return $this->collector;
    }

    /**
     * @return Dispatcher
     */
    public function getDispatcher(): Dispatcher
    {
        return $this->dispatcher;
    }

}