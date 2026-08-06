<?php

namespace DI;

class Application
{
    public function __construct(
        private Logger $logger,
        private CacheInterface $cache
    ) {}

    public function initialize(): string
    {
        $this->logger->write('./debug.log');
        $string = "The application has been initialized.";
        $string .= "\n" . $this->cache->initialize();
        return $string;
    }
}
