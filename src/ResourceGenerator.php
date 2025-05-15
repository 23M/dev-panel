<?php

namespace TTM\DevPanel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;
use TTM\DevPanel\ColumnStrategies\CastedColumnStrategy;
use TTM\DevPanel\ColumnStrategies\ColumnStrategyInterface;
use TTM\DevPanel\ColumnStrategies\DefaultColumnStrategy;
use TTM\DevPanel\FieldStrategies\BelongsToFieldStrategy;
use TTM\DevPanel\FieldStrategies\BooleanFieldStrategy;
use TTM\DevPanel\FieldStrategies\CastedFieldStrategy;
use TTM\DevPanel\FieldStrategies\DateFieldStrategy;
use TTM\DevPanel\FieldStrategies\DateTimeFieldStrategy;
use TTM\DevPanel\FieldStrategies\DefaultFieldStrategy;
use TTM\DevPanel\FieldStrategies\EnumFieldStrategy;
use TTM\DevPanel\FieldStrategies\FieldStrategyInterface;
use TTM\DevPanel\FieldStrategies\IntegerFieldStrategy;
use TTM\DevPanel\FieldStrategies\JsonFieldStrategy;
use TTM\DevPanel\FieldStrategies\TranslatedFieldStrategy;

class ResourceGenerator
{
    /** @var array<class-string<FieldStrategyInterface>> $fieldStrategies */
    private array $fieldStrategies;

    /** @var array<class-string<ColumnStrategyInterface>> $columnStrategies */
    private array $columnStrategies;

    public function __construct(private FileManager $fileManager)
    {
        $this->fieldStrategies = [
            ...Config::get('dev-panel.field_strategies'),
            TranslatedFieldStrategy::class,
            BelongsToFieldStrategy::class,
            BooleanFieldStrategy::class,
            IntegerFieldStrategy::class,
            JsonFieldStrategy::class,
            DateTimeFieldStrategy::class,
            DateFieldStrategy::class,
            CastedFieldStrategy::class,
            EnumFieldStrategy::class,
            DefaultFieldStrategy::class,
        ];

        $this->columnStrategies = [
            ...Config::get('dev-panel.column_strategies'),
            CastedColumnStrategy::class,
            DefaultColumnStrategy::class,
        ];
    }

    public function generateForModel(string $modelClass): void
    {
        $resourceClass = Str::studly(class_basename($modelClass)) . 'Resource';
        $pageListClass = Str::studly(class_basename($modelClass)) . 'List';
        $pageEditClass = 'Edit' . Str::studly(class_basename($modelClass));
        $pageCreateClass = 'Create' . Str::studly(class_basename($modelClass));

        $this->generateResource($modelClass, $resourceClass, $pageListClass, $pageEditClass, $pageCreateClass);
        $this->generateListPage($modelClass, $resourceClass, $pageListClass);
        $this->generateEditPage($modelClass, $resourceClass, $pageEditClass);
        $this->generateCreatePage($modelClass, $resourceClass, $pageCreateClass);
    }

    private function generateResource(string $modelClass, string $resourceClass, string $pageListClass, string $pageEditClass, string $pageCreateClass): void
    {
        $tableName = (new $modelClass())->getTable();
        $belongsToRelations = $this->getBelongsToRelations(new ReflectionClass($modelClass), $modelClass);
        $casts = (new $modelClass())->getCasts();
        $translatableAttributes = in_array('\Spatie\Translatable\HasTranslations', class_uses_recursive($modelClass))
            ? (new $modelClass())->getTranslatableAttributes()
            : [];
        $hasSoftDeletes = in_array(SoftDeletes::class, class_uses_recursive($modelClass));

        $formFields = [];
        $tableColumns = [];

        foreach (DB::getSchemaBuilder()->getColumnListing($tableName) as $dbColumnName) {
            $attribute = new Attribute(
                modelClass: $modelClass,
                name: $dbColumnName,
                dbType: DB::getSchemaBuilder()->getColumnType($tableName, $dbColumnName),
                cast: $casts[$dbColumnName] ?? null,
                belongsToRelation: $belongsToRelations[$dbColumnName] ?? null,
                hasTranslations: in_array($dbColumnName, $translatableAttributes)
            );

            $formFields[] = $this->getFieldCode($attribute);
            $tableColumns[] = $this->getColumnCode($attribute);
        }

        $code = "<?php\n\n" . View::make('dev-panel::resource', [
                'modelClass' => $modelClass,
                'resourceClass' => $resourceClass,
                'pageListClass' => $pageListClass,
                'pageCreateClass' => $pageCreateClass,
                'pageEditClass' => $pageEditClass,
                'formFields' => $formFields,
                'tableColumns' => $tableColumns,
                'hasSoftDeletes' => $hasSoftDeletes,
            ])->render();

        $this->fileManager->write($resourceClass, $code);
    }

    private function generateListPage(string $modelClass, string $resourceClass, string $pageClass): void
    {
        $code = "<?php\n\n" . View::make('dev-panel::page-list', [
                'resourceClass' => $resourceClass,
                'pageClass' => $pageClass,
                'modelClass' => $modelClass,
            ])->render();

        $this->fileManager->write($pageClass, $code);
    }

    private function generateEditPage(string $modelClass, string $resourceClass, string $pageClass): void
    {
        $code = "<?php\n\n" . View::make('dev-panel::page-edit', [
                'resourceClass' => $resourceClass,
                'pageClass' => $pageClass,
                'modelClass' => $modelClass,
            ])->render();

        $this->fileManager->write($pageClass, $code);
    }

    private function generateCreatePage(string $modelClass, string $resourceClass, string $pageClass): void
    {
        $code = "<?php\n\n" . View::make('dev-panel::page-create', [
                'resourceClass' => $resourceClass,
                'pageClass' => $pageClass,
                'modelClass' => $modelClass,
            ])->render();

        $this->fileManager->write($pageClass, $code);
    }

    private function getFieldCode(Attribute $attribute): string
    {
        foreach ($this->fieldStrategies as $strategy) {
            $strategy = app($strategy);

            if ($strategy->isApplicable($attribute)) {
                return $strategy->getFieldCode($attribute);
            }
        }
        throw new RuntimeException("No field strategy found for attribute {$attribute->name}");
    }

    private function getColumnCode(Attribute $attribute): string
    {
        foreach ($this->columnStrategies as $strategy) {
            $strategy = app($strategy);

            if ($strategy->isApplicable($attribute)) {
                return $strategy->getColumnCode($attribute);
            }
        }
        throw new RuntimeException("No column strategy found for attribute {$attribute->name}");
    }

    /**
     * @param ReflectionClass<Model> $reflection
     * @return Collection<string, string>
     */
    private function getBelongsToRelations(ReflectionClass $reflection, string $modelClass): Collection
    {
        return collect($reflection->getMethods(ReflectionMethod::IS_PUBLIC))
            ->filter(fn (ReflectionMethod $method) => $method->getReturnType() instanceof ReflectionNamedType && $method->getReturnType()->getName() === BelongsTo::class)
            ->filter(fn (ReflectionMethod $method) => $method->getDeclaringClass()->getName() === $modelClass)
            ->filter(fn (ReflectionMethod $method) => $method->getNumberOfParameters() === 0)
            ->mapWithKeys(fn (ReflectionMethod $method) => [
                (new $modelClass())->{$method->getName()}()->getForeignKeyName() => $method->getName(),
            ]);
    }
}
