<?php

namespace DI;

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
     * Registered service aliases
     *
     * @var array
     */
    private array $aliases = [];

    /**
     * Already instantiated services
     * Cached to avoid creating multiple instances of the same service
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
     * Registers a service alias
     *
     * @param string $identifier
     * @param string $alias
     * @return void
     */
    public function alias(string $identifier, string $alias): void
    {
        $this->aliases[$identifier] = $alias;
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

        if (isset($this->aliases[$identifier])) {
            $identifier = $this->aliases[$identifier];
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
        return $this->instantiated[$identifier];
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
            if (isset($this->aliases[$parameter])) {
                $parameter = $this->aliases[$parameter];
            }
            $instances[] = $this->instantiated[$parameter];
        }

        $this->instantiated[$identifier] = new $identifier(...$instances);
    }
}
