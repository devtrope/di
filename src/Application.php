<?php

namespace DI;

class Application
{
    public function __construct(
        private Logger $logger,
        private Cache $cache
    ) {}

    public function initialize(): string
    {
        $this->logger->write('./debug.log');
        return "The application has been initialized.";
    }
}
