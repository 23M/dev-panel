<?php

declare(strict_types=1);

namespace TTM\DevPanel;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ModelFinder
{
    /**
     * @param array<string, string> $modelPaths
     * @return array<class-string>
     */
    public function findModels(array $modelPaths, string $baseModelClass): array
    {
        $models = collect($modelPaths)
            ->flatMap(function ($namespace, $path) {
                return collect((new Filesystem())->allFiles($path))
                    ->map(function ($file) use ($namespace) {
                        return $namespace . '\\' . Str::replaceLast('.php', '', $file->getFilename());
                    });
            })
            ->filter(function ($modelClass) use ($baseModelClass) {
                return is_subclass_of($modelClass, $baseModelClass) && !(new \ReflectionClass($modelClass))->isAbstract();
            });

        return $models->all();
    }
}
