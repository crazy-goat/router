<?php
declare(strict_types=1);

namespace CrazyGoat\Router\Interfaces;

interface RouteInterface
{
    /**
     * @param string $str
     * @return bool
     */
    public function matches(string $str): bool;
}