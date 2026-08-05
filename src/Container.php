<?php

namespace DI;

use Exception;

class Container
{
    /**
     * Registered service definitions
     *
     * @var array
     */
    private array $definitions = [];

    /**
     * Already instantiated services.
     * Cached to avoid creating multiple instances of the same service.
     *
     * @var array
     */
    private array $instantiated = [];

    private array $building = [];

    /**
     * Registers a service definition
     *
     * @param string $identifier
     * @param callable $factory
     * @return void
     */
    public function register(string $identifier, callable $factory): void
    {
        $this->definitions[$identifier] = $factory;
    }

    /**
     * Returns the service associated with the given identifier
     *
     * @param string $identifier
     * @return mixed
     */
    public function get(string $identifier): mixed
    {
        if (false === isset($this->definitions[$identifier])) {
            throw new Exception(
                "The {$identifier} key does not exist"
            );
        }

        if (isset($this->instantiated[$identifier])) {
            return $this->instantiated[$identifier];
        }
        
        if (\in_array($identifier, $this->building)) {
            throw new Exception(
                "Circular dependency detected"
            );
        }
        $this->building[] = $identifier;

        $instance = $this->definitions[$identifier]($this);
        $this->instantiated[$identifier] = $instance;
        return $instance;
    }
}
