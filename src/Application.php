<?php

namespace DI;

class Application
{
    public function __construct(
        private Logger $logger,
        private CacheInterface $cache,
        private string $applicationName = "Test"
    ) {}

    public function initialize(): string
    {
        $this->logger->write('./debug.log');
        $string = "The application {$this->applicationName} has been initialized.";
        $string .= "\n" . $this->cache->initialize();
        return $string;
    }
}
