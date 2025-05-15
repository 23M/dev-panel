<?php

namespace TTM\DevPanel\Providers;

use Illuminate\Support\ServiceProvider;
use TTM\DevPanel\Console\GenerateResourcesCommand;
use TTM\DevPanel\FileManager;

class GeneratorProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'dev-panel');

        $this->publishes([
            __DIR__ . '/../../config/dev-panel.php' => config_path('dev-panel.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateResourcesCommand::class,
            ]);
        }

        (new FileManager())->loadGeneratedFiles();
    }
}
