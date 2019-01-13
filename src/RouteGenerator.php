<?php

namespace CrazyGoat\Router;

interface RouteGenerator
{
    /**
     * @param string $name
     * @param array $params
     * @return string
     */
    public function pathFor($name, $params = []);
}