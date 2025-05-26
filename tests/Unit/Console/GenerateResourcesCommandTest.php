<?php

use TTM\DevPanel\Console\GenerateResourcesCommand;
use TTM\DevPanel\ModelFinder;
use TTM\DevPanel\ResourceGenerator;

it('runs the generator for each model found', function () {
    config(['dev-panel.models' => [
        'Workbench\App\Models' => __DIR__ . '../../workbench/Models',
    ]]);

    $modelFinder = $this->mock(ModelFinder::class);
    $modelFinder->shouldReceive('findModels')
        ->once()
        ->with(['Workbench\App\Models' => __DIR__ . '../../workbench/Models'], 'Illuminate\Database\Eloquent\Model')
        ->andReturn([
            'Workbench\App\Models\User',
            'Workbench\App\Models\Post',
        ]);

    $resourceGenerator = $this->mock(ResourceGenerator::class);
    $resourceGenerator->shouldReceive('generateForModel')
        ->once()
        ->with('Workbench\App\Models\User')
        ->andReturnNull();
    $resourceGenerator->shouldReceive('generateForModel')
        ->once()
        ->with('Workbench\App\Models\Post')
        ->andReturnNull();

    $this->artisan(GenerateResourcesCommand::class)
        ->expectsOutput('Generating resource for: Workbench\App\Models\User')
        ->expectsOutput('Generating resource for: Workbench\App\Models\Post')
        ->assertExitCode(0);
});
