<?php
declare(strict_types=1);

namespace CrazyGoat\Router\Exceptions;

final class RouteNotFound extends \Exception
{
    public function __construct()
    {
        parent::__construct('No matching route found');
    }
}
