<?php
declare(strict_types=1);

namespace CrazyGoat\Router\Interfaces;

interface CacheProviderInterface
{
    public function load(): array;

    public function save(array $data): void;
}