<?php
declare(strict_types=1);

namespace CrazyGoat\Router\Provider;

use CrazyGoat\Router\Exceptions\CacheLoadException;
use CrazyGoat\Router\Exceptions\CacheNotFoundException;
use CrazyGoat\Router\Exceptions\CacheSaveException;
use CrazyGoat\Router\Exceptions\RouterFileReadException;
use CrazyGoat\Router\Interfaces\CacheProvider;
use CrazyGoat\Router\Interfaces\RoutingProvider;

final class FileCachedProvider implements RoutingProvider, CacheProvider
{
    /**
     * @var string
     */
    private $routingFile;
    /**
     * @var string
     */
    private $cacheFile;

    /** @var ?\Closure */
    private $routing = null;

    public function __construct(string $routingFile, string $cacheFile)
    {
        $this->routingFile = $routingFile;
        $this->cacheFile = $cacheFile;
    }

    public function load(): array
    {
        if (file_exists($this->cacheFile)) {
            try {
                return require $this->cacheFile;
            } catch (\Throwable $exception) {
                throw new CacheLoadException();
            }
        }
        throw new CacheNotFoundException();
    }

    public function save(array $data): void
    {
        $succes = file_put_contents(
            $this->cacheFile,
            '<?php return ' . var_export($data, true) . ';'
        );

        if ($succes === false) {
            throw new CacheSaveException();
        }
    }

    public function getRouting(): \Closure
    {
        if ($this->routing === null) {
            try {
                if (!file_exists($this->routingFile)) {
                    throw new RouterFileReadException();
                }

                $this->routing = require $this->routingFile;
            } catch (\Throwable $exception) {

            }
        }
        return $this->routing;
    }
}
