<?php
declare(strict_types=1);

namespace CrazyGoat\Router;

use CrazyGoat\Router\Interfaces\CacheProvider;
use CrazyGoat\Router\Interfaces\Dispatcher;
use CrazyGoat\Router\Interfaces\RoutingProvider;

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
     * @var RoutingProvider
     */
    private $provider;

    /**
     * Configuration constructor.
     * @param RoutingProvider $provider
     * @param RouteCollector $collector
     * @param Dispatcher $dispatcher
     */
    public function __construct(
        RoutingProvider $provider,
        RouteCollector $collector,
        Dispatcher $dispatcher
    ) {
        $this->provider = $provider;
        $this->collector = $collector;
        $this->dispatcher = $dispatcher;
    }

    public function getRoutingProvider(): RoutingProvider
    {
        return $this->provider;
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
