<?php

namespace DI;

class Cache implements CacheInterface
{
    public function initialize(): string
    {
        return "CACHE::INITIALIZED";
    }
}
