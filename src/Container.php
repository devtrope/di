<?php

namespace DI;

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
        if (isset($this->instantiated[$identifier])) {
            return $this->instantiated[$identifier];
        }

        $instance = $this->definitions[$identifier]($this);
        $this->instantiated[$identifier] = $instance;
        return $instance;
    }
}
