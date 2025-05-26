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

    private function getRelationColumns(Attribute $attribute): mixed
    {
        $relationTable = (new $attribute->modelClass())->{$attribute->belongsToRelation}()->getRelated()->getTable();
        return Schema::getColumnListing($relationTable);
    }

    /** @param array<string> $relationColumns */
    private function getTitleAttribute(array $relationColumns): string
    {
        foreach ([
            'name',
            'description',
            'label',
        ] as $column) {
            if (in_array($column, $relationColumns)) {
                return $column;
            }
        }

        return $relationColumns[0] ?? 'id';
    }
}
