<?php

namespace DI;

use Exception;
use ReflectionClass;
use ReflectionNamedType;

class Container
{
    /**
     * Registered service definitions
     *
     * @var array
     */
    private array $definitions = [];

    /**
     * Already instantiated services
     * Cached to avoid creating multiple instances of the same service
     *
     * @var array
     */
    private array $instantiated = [];

    /**
     * Services being built
     *
     * @var array
     */
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
        if (isset($this->instantiated[$identifier])) {
            return $this->instantiated[$identifier];
        }

        $parameters = [];
        $depencencies = $this->resolveDependencies($identifier);
        foreach ($depencencies as $depencency) {
            /**
             * @var ReflectionNamedType $type
             */
            $type = $depencency->getType();
            $parameter = $type->getName();
            $parameters[] = $parameter;
            $this->get($parameter);
        }

        $this->instantiate($identifier, $parameters);

        if (!isset($this->instantiated[$identifier])) {
            $this->instantiated[$identifier] = new $identifier($this->instantiated[$name]);
        }
        return $this->instantiated[$identifier];

        /*if (false === isset($this->definitions[$identifier])) {
            throw new Exception(
                "The {$identifier} key does not exist"
            );
        }

        if (isset($this->instantiated[$identifier])) {
            return $this->instantiated[$identifier];
        }
        
        if (isset($this->building[$identifier])) {
            throw new Exception(
                "Circular dependency detected"
            );
        }
        $this->building[$identifier] = true;

        try {
            $instance = $this->definitions[$identifier]($this);
        } catch (Exception $e) {
            throw $e;
        } finally {
            unset($this->building[$identifier]);
        }

        $this->instantiated[$identifier] = $instance;
        return $instance;*/
    }

    private function resolveDependencies(string $identifier): array
    {
        $reflectionClass = new ReflectionClass($identifier);
        $constructor = $reflectionClass->getConstructor();
        if (null === $constructor) {
            return [];
        }

        return $constructor->getParameters();
    }

    private function instantiate(string $identifier, array $parameters): void
    {
        if (empty($parameters)) {
            $this->instantiated[$identifier] = new $identifier();
            return;
        }

        $instances = [];
        foreach ($parameters as $parameter) {
            $instances[] = $this->instantiated[$parameter];
        }

        $this->instantiated[$identifier] = new $identifier(...$instances);
    }
}
