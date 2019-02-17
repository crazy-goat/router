<?php
declare(strict_types=1);

namespace CrazyGoat\Router\Provider;

use CrazyGoat\Router\Interfaces\RoutingProvider;

final class ClosureProvider implements RoutingProvider
{
    /**
     * @var \Closure
     */
    private $routing;

    public function __construct(\Closure $routing)
    {
        $this->routing = $routing;
    }

    public function getRouting(): \Closure
    {
        return $this->routing;
    }
}
