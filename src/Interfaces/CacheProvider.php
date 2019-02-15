<?php
declare(strict_types=1);

namespace CrazyGoat\Router\Interfaces;

interface CacheProvider
{
    public function load(): array;

    public function save(array $data): void;
}
