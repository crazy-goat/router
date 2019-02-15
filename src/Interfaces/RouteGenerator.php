<?php
declare(strict_types=1);

namespace CrazyGoat\Router\Interfaces;

interface RouteGenerator
{
    /**
     * @param string $name
     * @param array $params
     * @return string
     */
    public function pathFor(string $name, array $params = []): string;
}