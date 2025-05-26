<?php

use Illuminate\Database\Eloquent\Model;

it('returns a list of the models', function () {
    $modelFinder = new TTM\DevPanel\ModelFinder();

    $models = $modelFinder->findModels([
        __DIR__ . '/../../workbench/app/Models' => 'Workbench\\App\\Models',
    ], Model::class);

    expect($models)->toBeArray()
        ->toHaveCount(2)
        ->toContain('Workbench\\App\\Models\\User')
        ->toContain('Workbench\\App\\Models\\Post');
});
