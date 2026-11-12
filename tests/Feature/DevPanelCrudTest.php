<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Process\Process;
use TTM\DevPanel\ModelFinder;
use TTM\DevPanel\Testing\ResourceCrudTester;
use Workbench\App\Models\User;

/*
|--------------------------------------------------------------------------
| CRUD tests for every model of the dev panel
|--------------------------------------------------------------------------
|
| The dev panel resources are generated code that Filament discovers while the application boots.
| So - exactly like in a real application - they are generated first (in a separate process
| against a throw-away database) and only then the application under test is booted.
|
*/

const WORKBENCH_MODEL_PATH = __DIR__ . '/../../workbench/app/Models';
const WORKBENCH_MODEL_NAMESPACE = 'Workbench\\App\\Models';

beforeAll(function (): void {
    $database = tempnam(sys_get_temp_dir(), 'dev-panel-') . '.sqlite';
    touch($database);

    // The skeleton's "testing" connection is hardwired to an in-memory database, which would not survive between the processes.
    $env = ['DB_CONNECTION' => 'sqlite', 'DB_DATABASE' => $database];
    $testbench = [PHP_BINARY, __DIR__ . '/../../vendor/bin/testbench'];

    foreach ([['migrate', '--force'], ['dev-panel:generate']] as $command) {
        $process = new Process([...$testbench, ...$command], dirname(__DIR__, 2), $env);
        $process->mustRun();
    }

    unlink($database);
});

/** @return array<class-string<Model>, array{class-string<Model>}> */
function devPanelModels(): array
{
    $models = (new ModelFinder())->findModels([WORKBENCH_MODEL_PATH => WORKBENCH_MODEL_NAMESPACE], Model::class);

    return collect($models)->mapWithKeys(fn (string $model): array => [$model => [$model]])->all();
}

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('tests the same models that the dev panel is configured with', function (): void {
    $configured = (new ModelFinder())->findModels(config('dev-panel.models'), config('dev-panel.base_model_class'));

    expect($configured)->toEqualCanonicalizing(array_keys(devPanelModels()));
});

it('lists the records of :dataset', function (string $model): void {
    $tester = ResourceCrudTester::for($model);

    if (! $tester->hasFactory()) {
        $this->markTestSkipped("{$model} has no factory");
    }

    $tester->assertCanList();
})->with(devPanelModels());

it('creates a record of :dataset', function (string $model): void {
    $tester = ResourceCrudTester::for($model);

    if (! $tester->hasFactory()) {
        $this->markTestSkipped("{$model} has no factory");
    }

    $tester->assertCanCreate();
})->with(devPanelModels());

it('edits a record of :dataset', function (string $model): void {
    $tester = ResourceCrudTester::for($model);

    if (! $tester->hasFactory()) {
        $this->markTestSkipped("{$model} has no factory");
    }

    $tester->assertCanEdit();
})->with(devPanelModels());

it('deletes a record of :dataset', function (string $model): void {
    $tester = ResourceCrudTester::for($model);

    if (! $tester->hasFactory()) {
        $this->markTestSkipped("{$model} has no factory");
    }

    $tester->assertCanDelete();
})->with(devPanelModels());
