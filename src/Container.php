<?php

namespace DI;

use Exception;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

class Container
{
    /**
     * Registered service aliases
     *
     * @var array
     */
    private array $aliases = [];

    /**
     * Registered service bindings
     *
     * @var array
     */
    private array $bindings = [];

    /**
     * Already instantiated services
     * Cached to avoid creating multiple instances of the same service
     *
     * @var array
     */
    private array $instantiated = [];

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
        $dependencies = $this->resolveDependencies($identifier);
        /**
         * @var ReflectionParameter $dependency
         */
        foreach ($dependencies as $dependency) {
            // If there is a default value, we register it automatically
            if ($dependency->isDefaultValueAvailable()) {
                $parameters[] = $dependency->getDefaultValue();
                continue;
            }

            if (isset($this->bindings[$dependency->getName()])) {
                $parameters[] = $this->bindings[$dependency->getName()];
                continue;
            }

            /**
             * @var ReflectionNamedType $type
             */
            $type = $dependency->getType();
            $parameter = $type->getName();
            $parameters[] = $parameter;
            $this->get($parameter);
        }

        $this->instantiate($identifier, $parameters);
        return $this->instantiated[$identifier];
    }

    public function load(string $configurationFile): void
    {
        if (false === is_readable($configurationFile)) {
            throw new Exception(
                "The configuration file {$configurationFile} is not readable"
            );
        }

        $configuration = require $configurationFile;
        foreach ($configuration as $key => $values) {
            foreach ($values as $valuesKey => $value) {
                if ('alias' === $valuesKey) {
                    $this->aliases[$key] = $value;
                }
            }
        }
    }

    private function resolveDependencies(string $identifier): array
    {
        if (false === class_exists($identifier)) {
            return [];
        }

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

            if (false === class_exists($parameter)) {
                $instances[] = $parameter;
                continue;
            }

            $instances[] = $this->instantiated[$parameter];
        }

        $this->instantiated[$identifier] = new $identifier(...$instances);
    }
}
