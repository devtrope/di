<?php

namespace DI;

class Container
{
    private array $definitions = [];

    public function register(string $identifier, callable $factory): void
    {
        $this->definitions[$identifier] = $factory;
    }

    public function get(string $identifier): mixed
    {
        return $this->definitions[$identifier]($this);
    }
}
