<?php
declare(strict_types=1);

namespace CrazyGoat\Router\Interfaces;

interface RoutingProvider
{
    public function getRouting(): \Closure;
}
