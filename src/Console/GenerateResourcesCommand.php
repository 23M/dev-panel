<?php

namespace TTM\DevPanel\Console;

use Illuminate\Console\Command;
use TTM\DevPanel\ModelFinder;
use TTM\DevPanel\ResourceGenerator;

class GenerateResourcesCommand extends Command
{
    protected $signature = 'filament:generate-resources';
    protected $description = 'Generate Filament resources from models';

    public function handle(ModelFinder $modelFinder, ResourceGenerator $generator): void
    {
        $models = $modelFinder->findModels(
            (array) config('dev-panel.models', []),
            (string) config('dev-panel.base_model_class', 'Illuminate\Database\Eloquent\Model')
        );

        foreach ($models as $modelClass) {
            if (class_exists($modelClass)) {
                $this->info("Generating resource for: $modelClass");
                $generator->generateForModel($modelClass);
            }
        }
    }
}
