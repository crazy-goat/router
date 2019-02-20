<?php
declare(strict_types=1);

namespace CrazyGoat\Router\Exceptions;

final class MethodNotAllowed extends \Exception
{
    /**
     * @var array
     */
    private $allowedMethods;

    public function __construct(string $method, array $allowedMethods)
    {
        parent::__construct(
            sprintf('Method %s not allowed. Allowed methods: [%s]', $method, implode(', ', $allowedMethods))
        );
        $this->allowedMethods = $allowedMethods;
    }

    /**
     * @return array
     */
    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }
}
