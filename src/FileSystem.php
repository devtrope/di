<?php

namespace DI;

class FileSystem
{
    public function fileExists(string $filename): bool
    {
        return file_exists($filename);
    }
}
