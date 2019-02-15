<?php
declare(strict_types=1);

namespace CrazyGoat\Router\Cache;

use CrazyGoat\Router\Exceptions\CacheSaveException;
use CrazyGoat\Router\Interfaces\CacheProviderInterface;

class File implements CacheProviderInterface {
    /**
     * @var string
     */
    private $cacheFile;

    public function __construct(string $cacheFile)
    {
        $this->cacheFile = $cacheFile;
    }

    public function load(): array
    {
        if (file_exists($this->cacheFile) && is_readable($this->cacheFile)) {
            $data = require $this->cacheFile;
            return is_array($data) ? $data : null;
        }
        throw new \CrazyGoat\Router\Exceptions\CacheLoadException();
    }

    public function save(array $data): void
    {
        $status = file_put_contents(
            $this->cacheFile,
            '<?php return ' . var_export($data, true) . ';'
        );

        if ($status === false) {
            throw new CacheSaveException();
        }
    }
}