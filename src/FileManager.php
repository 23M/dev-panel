<?php

declare(strict_types=1);

namespace TTM\DevPanel;

class FileManager
{
    private string $path;

    public function __construct()
    {
        $this->path = app()->bootstrapPath('cache/filament-resources');

        if (!is_dir($this->path)) {
            mkdir($this->path, 0755, true);
        }
    }

    public function write(string $className, string $content): void
    {
        file_put_contents($this->path . '/' . $className . '.php', $content);
    }

    public function loadGeneratedFiles(): void
    {
        foreach (glob($this->path . '/*.php') as $file) {
            require_once $file;
        }
    }
}
