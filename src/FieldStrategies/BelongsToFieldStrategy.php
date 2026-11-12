<?php

declare(strict_types=1);

namespace TTM\DevPanel\FieldStrategies;

use Illuminate\Support\Facades\Schema;
use TTM\DevPanel\Attribute;

class BelongsToFieldStrategy implements FieldStrategyInterface
{
    public function isApplicable(Attribute $attribute): bool
    {
        return $attribute->belongsToRelation !== null;
    }

    public function getFieldCode(Attribute $attribute): string
    {
        $relationColumns = $this->getRelationColumns($attribute);
        $titleAttribute = $this->getTitleAttribute($relationColumns);

        return <<<PHP
            Select::make('{$attribute->name}')
                ->relationship('{$attribute->belongsToRelation}', '{$titleAttribute}')
                ->searchable(),
        PHP;
    }

    /** @return array<string, bool> nullable flag by column name */
    private function getRelationColumns(Attribute $attribute): array
    {
        $relationTable = (new $attribute->modelClass())->{$attribute->belongsToRelation}()->getRelated()->getTable();

        $columns = [];

        foreach (Schema::getColumns($relationTable) as $column) {
            $columns[$column['name']] = (bool) $column['nullable'];
        }

        return $columns;
    }

    /**
     * The first of the usual title columns that is always filled. A record without a title is not a valid
     * option of the select, so a column that can be empty is not used, the first column (the key) is.
     *
     * @param array<string, bool> $relationColumns
     */
    private function getTitleAttribute(array $relationColumns): string
    {
        foreach (['name', 'title', 'subject', 'label', 'description'] as $column) {
            if (isset($relationColumns[$column]) && ! $relationColumns[$column]) {
                return $column;
            }
        }

        return array_key_first($relationColumns) ?? 'id';
    }
}
