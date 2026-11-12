<?php

declare(strict_types=1);

namespace TTM\DevPanel\Testing;

use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use PHPUnit\Framework\Assert;
use RuntimeException;

/**
 * Exercises the generated dev panel resource of a model: list, create, edit and delete.
 *
 * Record data is taken from the model's factory, so the model has to use `HasFactory`.
 * The panel's resources have to be generated (`dev-panel:generate`) before the application boots,
 * and the test needs an authenticated user that is allowed to use the panel.
 */
final class ResourceCrudTester
{
    public const PANEL_ID = 'devPanel';

    /** @param class-string<Model> $modelClass */
    private function __construct(private readonly string $modelClass)
    {
    }

    /** @param class-string<Model> $modelClass */
    public static function for(string $modelClass): self
    {
        return new self($modelClass);
    }

    /** @return class-string<Resource> */
    public function resource(): string
    {
        $panel = Filament::getPanel(self::PANEL_ID);
        Filament::setCurrentPanel($panel);

        foreach ($panel->getResources() as $resource) {
            if ($resource::getModel() === $this->modelClass) {
                return $resource;
            }
        }

        throw new RuntimeException("No dev panel resource found for {$this->modelClass}. Did you run dev-panel:generate before booting the application?");
    }

    public function hasFactory(): bool
    {
        return method_exists($this->modelClass, 'factory');
    }

    public function assertCanList(): void
    {
        $record = $this->createRecord();

        $page = Livewire::test($this->page('index', ListRecords::class))
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$record]);

        Assert::assertNotEmpty($page->instance()->getTable()->getColumns(), "The dev panel table of {$this->modelClass} has no columns.");
    }

    public function assertCanCreate(): void
    {
        $existingKeys = $this->newQuery()->pluck((new $this->modelClass())->getKeyName())->all();

        $page = Livewire::test($this->page('create', CreateRecord::class))
            ->assertSuccessful();

        $state = $this->makeFormState($page);

        $page->fillForm($state)
            ->call('create')
            ->assertHasNoFormErrors();

        $created = $this->newQuery()->whereKeyNot($existingKeys)->get();

        Assert::assertCount(1, $created, "Creating a {$this->modelClass} via the dev panel did not create exactly one record.");

        $this->assertTextFieldsPersisted($page, $created->firstOrFail(), $state);
    }

    public function assertCanEdit(): void
    {
        $record = $this->createRecord();
        $originalRow = $this->databaseRow($record);

        $page = Livewire::test($this->page('edit', EditRecord::class), ['record' => $record->getRouteKey()])
            ->assertSuccessful();

        $this->formFields($page);

        // Saving the unchanged form must not alter the record (values survive the form round trip).
        $page->call('save')->assertHasNoFormErrors();

        Assert::assertSame(
            $originalRow,
            $this->databaseRow($record),
            "Saving the unchanged {$this->modelClass} edit form changed the record.",
        );

        // Changing the values must be persisted.
        $state = $this->makeFormState($page);

        $page->fillForm($state)->call('save')->assertHasNoFormErrors();

        $this->assertTextFieldsPersisted($page, $record->refresh(), $state);
    }

    public function assertCanDelete(): void
    {
        $record = $this->createRecord();

        $list = Livewire::test($this->page('index', ListRecords::class))
            ->assertCanSeeTableRecords([$record])
            ->callTableBulkAction('delete', [$record]);

        if (! $this->usesSoftDeletes()) {
            Assert::assertNull($this->newQuery()->find($record->getKey()), "Deleting a {$this->modelClass} via the dev panel did not delete the record.");

            return;
        }

        Assert::assertTrue($this->isTrashed($record), "Deleting a {$this->modelClass} via the dev panel did not soft delete the record.");

        $list->filterTable('trashed', true)
            ->assertCanSeeTableRecords([$record])
            ->callTableBulkAction('restore', [$record]);

        Assert::assertFalse($this->isTrashed($record), "Restoring a {$this->modelClass} via the dev panel did not restore the record.");

        $list->callTableBulkAction('delete', [$record])
            ->filterTable('trashed', true)
            ->callTableBulkAction('forceDelete', [$record]);

        Assert::assertNull($this->findWithTrashed($record), "Force deleting a {$this->modelClass} via the dev panel did not remove the record.");
    }

    /** @return Factory<Model> */
    private function factory(): Factory
    {
        $factory = call_user_func([$this->modelClass, 'factory']);

        Assert::assertInstanceOf(Factory::class, $factory, "{$this->modelClass}::factory() does not return a factory.");

        return $factory;
    }

    private function createRecord(): Model
    {
        $record = $this->factory()->create();

        Assert::assertInstanceOf(Model::class, $record);

        return $record;
    }

    /**
     * The form state of a new record built by the model's factory, in the shape the form's fields expect.
     *
     * @param Testable<CreateRecord|EditRecord> $page
     * @return array<string, mixed>
     */
    private function makeFormState(Testable $page): array
    {
        $model = $this->factory()->make();

        Assert::assertInstanceOf(Model::class, $model);

        $state = $model->makeVisible($model->getHidden())->toArray();

        foreach ($this->formFields($page) as $name => $field) {
            // A text field can only hold a string, so structured values (e.g. JSON columns) are typed in as JSON.
            if (is_array($state[$name] ?? null) && ($field instanceof TextInput || $field instanceof Textarea)) {
                $state[$name] = json_encode($state[$name]);
            }
        }

        return $state;
    }

    /**
     * @param Testable<CreateRecord|EditRecord> $page
     * @return array<string, \Filament\Forms\Components\Field>
     */
    private function formFields(Testable $page): array
    {
        $form = $page->instance()->getSchema('form');

        Assert::assertInstanceOf(Schema::class, $form, "The dev panel page of {$this->modelClass} has no form.");

        $fields = $form->getFlatFields();

        Assert::assertNotEmpty($fields, "The dev panel form of {$this->modelClass} has no fields.");

        return $fields;
    }

    /**
     * Asserts that plain text inputs of the form (the ones without a cast) hold the submitted value in the database.
     *
     * @param Testable<CreateRecord|EditRecord> $page
     * @param array<string, mixed> $state
     */
    private function assertTextFieldsPersisted(Testable $page, Model $record, array $state): void
    {
        foreach ($this->formFields($page) as $name => $field) {
            $isPlainText = ($field instanceof TextInput && ! $field->isNumeric() && ! $field->isPassword())
                || $field instanceof Textarea;

            if (! $isPlainText || ! is_string($state[$name] ?? null) || $record->hasCast($name)) {
                continue;
            }

            Assert::assertSame($state[$name], $record->getAttribute($name), "The field '{$name}' of {$this->modelClass} was not persisted by the dev panel.");
        }
    }

    /**
     * The record as stored in the database (without `updated_at`, which changes on every save).
     *
     * @return array<string, mixed>
     */
    private function databaseRow(Model $record): array
    {
        $row = $this->newQuery()
            ->withoutGlobalScopes()
            ->whereKey($record->getKey())
            ->firstOrFail()
            ->getAttributes();

        unset($row[$record->getUpdatedAtColumn()]);
        ksort($row);

        return $row;
    }

    /**
     * @template T of object
     * @param class-string<T> $expectedPageClass
     * @return class-string<T>
     */
    private function page(string $name, string $expectedPageClass): string
    {
        $pages = $this->resource()::getPages();

        Assert::assertArrayHasKey($name, $pages, "The dev panel resource of {$this->modelClass} has no '{$name}' page.");

        $page = $pages[$name]->getPage();

        Assert::assertTrue(is_subclass_of($page, $expectedPageClass), "The '{$name}' page of {$this->modelClass} is not a {$expectedPageClass}.");

        return $page;
    }

    /** @return Builder<Model> */
    private function newQuery(): Builder
    {
        return $this->modelClass::query();
    }

    private function findWithTrashed(Model $record): ?Model
    {
        return $this->newQuery()
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->whereKey($record->getKey())
            ->first();
    }

    private function isTrashed(Model $record): bool
    {
        $stored = $this->findWithTrashed($record);

        Assert::assertNotNull($stored, "The {$this->modelClass} record does not exist anymore.");

        return (bool) call_user_func([$stored, 'trashed']);
    }

    private function usesSoftDeletes(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($this->modelClass), true);
    }
}
