<?php

namespace DI;

use Exception;

class Logger
{
    public function __construct(private FileSystem $fileSystem)
    {}

    public function write(string $filename): void
    {
        if (false === $this->fileSystem->fileExists($filename)) {
            throw new Exception("The file does not exists");
        }

        $resource = fopen($filename, 'w');
        fwrite($resource, 'test');
        fclose($resource);
    }
}
